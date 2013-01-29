<?php

/**
 * intervention actions.
 *
 * @package    cpc
 * @subpackage loi
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loiActions extends sfActions
{

  private function getAmendements($loi, $articles = 'all', $alineas = 0) {
    $amendements = array();
    $admts = Doctrine_Query::create()
      ->select('a.*, (sum(a.nb_multiples)-1) as identiques, CAST( a.numero AS SIGNED ) AS num')
      ->from('Amendement a')
      ->where('a.texteloi_id = ?', $loi)
      ->andWhere('a.sort <> ?', 'Rectifié')
      ->groupBy('a.content_md5')
      ->orderBy('a.content_md5, a.sort, num');
    if ($articles != 'all') {
      $likestr = '';
      foreach ($articles as $article) {
        $like = 'a.sujet LIKE "%art% '.preg_replace('/1.?er?/', 'premier', $article->titre).'%"';
	if (preg_match("/(1.?er?|premier)(.*)$/i", $article->titre, $match))
          $like .= ' OR a.sujet LIKE "titre" OR a.sujet LIKE "%art% 1e%"';
        if ($likestr == '') $likestr = $like;
        else $likestr .= ' OR '.$like;
      }
      if ($likestr != '') $admts->andWhere($likestr);
    }
    foreach ($admts->fetchArray() as $adt) {
      $art = str_replace("È", "è", preg_replace('/premier/', '1er', strtolower($adt['sujet'])));
      $art = trim(preg_replace("/[l'\s]*art(\.|icle)?\s*/", ' ', $art));
      $content = $adt['numero'];
      if ($adt['identiques'])
        $content .= ' <small>('.$adt['identiques'].' identique'.($adt['identiques'] > 1 ? 's' : '').')</small>';
      if (preg_match('/(adopté|favorable)/i', $adt['sort'], $match)) $content .= ' <b>'.strtolower($match[1]).'</b>';
      $add = array($content);
      if (isset($amendements[$art])) {
        $amendements[$art] = array_merge($amendements[$art], $add);
        $amendements[$art.'tot'] += $adt['identiques']+1;
      } else {
        $amendements[$art] = $add;
        $amendements[$art.'tot'] = $adt['identiques']+1;
      }
      if ($alineas && !(preg_match('/(avant|après)/', $art)) && preg_match("/alin..?as?\D\D?(\d+)\D/", $adt['texte'], $match)) {
        $al = $art.'-'.$match[1];
        if (isset($amendements[$al])) {
          $amendements[$al] = array_merge($amendements[$al], $add);
          $amendements[$al.'tot'] += $adt['identiques']+1;
        } else {
          $amendements[$al] = $add;
          $amendements[$al.'tot'] = $adt['identiques']+1;
        }
      }
    } 
    return $amendements;
  }

 
  public function executeLoi(sfWebRequest $request) {
    $loi_id = $this->getLoi($request);
    $this->soussections = Doctrine::getTable('TitreLoi')->createquery('t')
      ->where('t.texteloi_id = ?', $loi_id)
      ->andWhere('t.id != t.titre_loi_id')
      ->orderBy('t.level1, t.level2, t.level3, t.level4')
      ->execute();
    $this->articles = Doctrine::getTable('ArticleLoi')->createquery('a')
      ->where('a.texteloi_id = ?', $loi_id)
      ->orderBy('a.ordre')
      ->fetchArray();
    $this->amendements = count(Doctrine::getTable('Amendement')->createquery('a')
      ->where('a.texteloi_id = ?', $loi_id)
      ->andWhere('a.sort <> ?', 'Rectifié')
      ->execute());
    if ($this->loi->getDossier())
      $this->dossier = $this->loi->getDossier()->id;
    $this->doc = Doctrine::getTable('Texteloi')->findOneBySource($this->loi->source);
    $this->response->setTitle("Simplifions la loi - ".strip_tags($this->loi->titre).' - NosDéputés.fr');
    $request->setParameter('rss', array(array('link' => '@loi_rss_commentaires?loi='.$loi_id, 'title'=>'Les commentaires sur '.$this->loi->titre)));
  }

  public function executeSection(sfWebRequest $request)
  {
    $loi_id = $this->getLoi($request, 1);
    $levels = array();
    for ($i = 1; $i < 5; $i++)
      $levels[] = $request->getParameter('level'.$i, 0);
    $this->section = Doctrine::getTable('TitreLoi')->identifyAndFindLevel($loi_id, $levels);
    $this->forward404Unless($this->section);
    $this->level = $this->section->getLevel();
    $qss = Doctrine::getTable('TitreLoi')->createquery('t')
      ->where('t.texteloi_id = ?', $loi_id);
    for ($i = 1; $i <= $this->level; $i++)
      $qss->andWhere('t.level'.$i.' = ?', $levels[$i-1]);
    if($this->level < 4)
      $qss->andWhere('t.level'.($this->level+1).' IS NOT NULL');
    $this->soussections = $qss->orderBy('t.level1, t.level2, t.level3, t.level4')
      ->execute();
    $ids = array($this->section->id);
    if ($this->soussections) foreach ($this->soussections as $ss) $ids[] = $ss->id;
    $this->articles = Doctrine::getTable('ArticleLoi')
      ->createquery('a')
      ->whereIn('a.titre_loi_id', $ids)
      ->andWhere('a.texteloi_id = ?', $loi_id)
      ->orderBy('a.ordre')
      ->execute();
    if (count($this->articles) == 1)
      $this->redirect('@loi_article?loi='.$loi_id.'&article='.$this->articles[0]->slug);
    $this->amendements = $this->getAmendements($loi_id, $this->articles);
    $this->voisins = $this->section->getVoisins();
    $this->titre = $this->section->getHierarchie()." : ".$this->section->titre;
    $this->response->setTitle(strip_tags($this->loi->titre.' - '.$this->titre));
  }

 public function executeAlinea(sfWebRequest $request) {
    $id = $request->getParameter('id');
    $this->alinea = Doctrine::getTable('Alinea')->find($id);
    $this->forward404Unless($this->alinea);
    $article = $this->alinea->getArticle();
    $this->forward404Unless($article);
    $this->loi = Doctrine::getTable('TitreLoi')->findLightLoi($article->texteloi_id);
    $loi_id = $this->loi->texteloi_id;
    $index = array();
    for ($i = $this->alinea->numero - 3; $i < $this->alinea->numero + 4; $i++) {
      if ($i > 0) $index[] = $i;
    }
    $this->alineas = Doctrine::getTable('Alinea')
      ->createquery('a')
      ->where('a.article_loi_id = ?', $article->id)
      ->andWhereIn('a.numero', $index) 
      ->orderBy('a.numero')
      ->execute();
    $this->forward404Unless(count($this->alineas));
    $this->titre_article = $article->titre;
    $this->slug_article = $article->slug;
    $this->titre = 'Article '.$this->titre_article.' - Alinea '.$this->alinea->numero;
    $this->response->setTitle($this->titre);
  }

  public function executeArticle(sfWebRequest $request) {
    if ($id = $request->getParameter('id'))
      $this->article = Doctrine::getTable('ArticleLoi')->find($id);
    else {
      $loi_id = $this->getLoi($request, 1);
      $slug_article = $request->getParameter('article');
      $this->article = Doctrine::getTable('ArticleLoi')->findOneByLoiSlug($loi_id, $slug_article);
    }
    $this->forward404Unless($this->article);
    if (!(isset($slug_article))) {
      $this->loi = Doctrine::getTable('TitreLoi')->findLightLoi($this->article->texteloi_id);
      $loi_id = $this->loi->texteloi_id;
    }
    $this->alineas = Doctrine::getTable('Alinea')
      ->createquery('a')
      ->where('a.article_loi_id = ?', $this->article->id)
      ->orderBy('a.numero')
      ->execute();
    $this->amendements = $this->getAmendements($loi_id, array($this->article), 1);
    $this->forward404Unless(count($this->alineas));
    $this->section = $this->article->getTitreLoi();
    $titre = strip_tags($this->loi->titre);
    if ($this->section->getHierarchie())
      $titre .= " (".strip_tags($this->section->getHierarchie()).") ";
    $this->titre = 'Article '.$this->article->titre;
    $this->response->setTitle($titre.' - '.$this->titre);
    if (isset($this->article->expose) && $this->article->expose != "") $this->expose = $this->article->expose;
    else $this->expose = $this->section->expose;
  }

  public function getLoi($request, $light = 0) {
    $loi_id = $request->getParameter('loi');
    if ($light)
      $this->loi = Doctrine::getTable('TitreLoi')->findLightLoi($loi_id);
    else $this->loi = Doctrine::getTable('TitreLoi')->findLoi($loi_id);
    $this->forward404Unless($this->loi);
    return $loi_id;
  }

  public function executeRedirect(sfWebRequest $request) {
	$loi = $request->getParameter('loi');
	$article = $request->getParameter('article');
	if (preg_match('/^n°/', $loi)) {
		$loi = 'loi '.$loi;
	}else if (!preg_match('/^(code|loi|libre)/', $loi)) {
		$loi ='code '.$loi;
	}
	if (preg_match('/^(code|livre)/', $loi) && $article) {
    		foreach (Alinea::$code_legif as $code => $legif) if (preg_match('/'.$code.'/', $loi)) {
      			return $this->redirect('http://www.legifrance.gouv.fr/rechCodeArticle.do?champCode='.$legif.'&champNumArticle='.$article);
    		}
	}
	return $this->redirect('http://www.google.fr/search?btnI=1&q=site%3Alegifrance.gouv.fr+'.urlencode($loi.' article '.$article));
  }

}

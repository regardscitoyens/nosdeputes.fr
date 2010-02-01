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
  public function executeLoi(sfWebRequest $request)
  {
    $loi_id = $this->getLoi($request);
    $this->soussections = doctrine::getTable('TitreLoi')->createquery('t')
      ->where('t.texteloi_id = ?', $loi_id)
      ->andWhere('t.id != t.titre_loi_id')
      ->orderBy('t.chapitre, t.section')
      ->execute();
    if (!$this->soussections) {
      $this->articles = doctrine::getTable('ArticleLoi')->createquery('a')
        ->where('a.texteloi_id = ?', $loi_id)
        ->orderBy('a.ordre')
        ->execute();
    }
    $this->response->setTitle(strip_tags($this->loi->titre).' - NosDéputés.fr');
    $request->setParameter('rss', array(array('link' => '@loi_rss_commentaires?loi='.$loi_id, 'title'=>'Les commentaires sur '.$this->loi->titre)));

  }

  public function executeSection(sfWebRequest $request)
  {
    $loi_id = $this->getLoi($request, 1);
    $n_chapitre = $request->getParameter('chapitre');
    $this->chapitre = doctrine::getTable('TitreLoi')->findChapitre($loi_id, $n_chapitre);
    $this->forward404Unless($this->chapitre);
    $n_section = $request->getParameter('section');
    $artquery = doctrine::getTable('ArticleLoi')->createquery('a');
    if ($n_section && $n_section != 0) {
      $this->section = doctrine::getTable('TitreLoi')->findSection($loi_id, $n_chapitre, $n_section);
      $this->forward404Unless($this->section);
      $artquery->where('a.titre_loi_id = ?', $this->section->id);
    } else {
      $this->soussections = doctrine::getTable('TitreLoi')->createquery('t')
        ->where('t.texteloi_id = ?', $loi_id)
        ->andWhere('t.chapitre = ?', $n_chapitre)
        ->andWhere('t.section IS NOT NULL')
        ->orderBy('t.section')
        ->execute();
      $ids = array();
      $ids[] = $this->chapitre->id;
      if ($this->soussections) foreach ($this->soussections as $ss) $ids[] = $ss->id;
      $artquery->whereIn('a.titre_loi_id', $ids);
    }
    $artquery->andWhere('a.texteloi_id = ?', $loi_id)
      ->orderBy('a.ordre');
    $this->articles = $artquery->execute();
    if (count($this->articles) == 1)
      $this->redirect('@loi_article?loi='.$loi_id.'&article='.$this->articles[0]->slug);
    if (isset($this->section)) {
      $titre = $this->section->getLargeTitre();
      if (doctrine::getTable('TitreLoi')->findSection($loi_id, $n_chapitre, $n_section+1))
        $this->suivant = $n_section + 1;
    } else {
      $titre = $this->chapitre->getLargeTitre();
      if (preg_match('/^(\d+)\s+bis$/',$n_chapitre, $match)) {
        $this->precedent = $match[1];
         if (doctrine::getTable('TitreLoi')->findChapitre($loi_id, $match[1]+1)) $this->suivant = $match[1]+1;
      } else {
        $pre = $n_chapitre - 1;
        $voisins = doctrine::getTable('TitreLoi')->createQuery('c')
          ->select('c.chapitre')
          ->where('c.texteloi_id = ?', $loi_id)
          ->andWhere('c.section is NULL')
          ->andWhereIn('c.chapitre', array($pre, $pre." bis", $n_chapitre." bis", $n_chapitre+1))
          ->orderBy('c.chapitre')
          ->fetchArray();
        $ct = count($voisins);
        if ($ct == 1) {
          if ($n_chapitre == 1) $this->suivant = $voisins[0]['chapitre'];
          else $this->precedent = $voisins[0]['chapitre'];
        } else if ($ct == 2) {
          $this->precedent = $voisins[0]['chapitre'];
          $this->suivant = $voisins[1]['chapitre'];
        } else if ($ct > 2) {
          if (preg_match('/bis/', $voisins[1]['chapitre']) && preg_match('/bis/', $voisins[2]['chapitre'])) {
            $this->precedent = $voisins[1]['chapitre'];
            $this->suivant = $voisins[2]['chapitre'];
          } else {
            $this->precedent = $voisins[0]['chapitre'];
            if (preg_match('/'.$n_chapitre.'/', $voisins[1]['chapitre'])) {
              $this->precedent = $voisins[0]['chapitre'];
              $this->suivant = $voisins[1]['chapitre'];
            } else {
              $this->precedent = $voisins[1]['chapitre'];
              $this->suivant = $voisins[2]['chapitre'];
            }
          }
        }
      }
    }
    $this->response->setTitle(strip_tags($this->loi->titre.' - '.$titre));
  }

 public function executeAlinea(sfWebRequest $request) {
    $id = $request->getParameter('id');
    $this->alinea = doctrine::getTable('Alinea')->find($id);
    $this->forward404Unless($this->alinea);
    $article = $this->alinea->getArticle();
    $this->forward404Unless($article);
    $this->loi = doctrine::getTable('TitreLoi')->findLightLoi($article->texteloi_id);
    $loi_id = $this->loi->texteloi_id;
    $index = array();
    for ($i = $this->alinea->numero - 3; $i < $this->alinea->numero + 4; $i++) {
      if ($i > 0) $index[] = $i;
    }
    $this->alineas = doctrine::getTable('Alinea')
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
      $this->article = doctrine::getTable('ArticleLoi')->find($id);
    else {
      $loi_id = $this->getLoi($request, 1);
      $slug_article = $request->getParameter('article');
      $this->article = doctrine::getTable('ArticleLoi')->findOneByLoiSlug($loi_id, $slug_article);
    }
    $this->forward404Unless($this->article);
    if (!(isset($slug_article))) {
      $this->loi = doctrine::getTable('TitreLoi')->findLightLoi($this->article->texteloi_id);
      $loi_id = $this->loi->texteloi_id;
    }
    $this->alineas = doctrine::getTable('Alinea')
      ->createquery('a')
      ->where('a.article_loi_id = ?', $this->article->id)
      ->orderBy('a.numero')
      ->execute();
    $this->forward404Unless(count($this->alineas));
    $this->section = $this->article->getTitreLoi();
    $this->titre = 'Article '.$this->article->titre;
    if (isset($this->section->chapitre) && $this->section->chapitre != 0)
      $this->titre .= ' ('.$this->section->getLargeTitre().')';
    $this->response->setTitle(strip_tags($this->loi->titre.' - '.$this->titre));
    if (isset($this->article->expose) && $this->article->expose != "") $this->expose = $this->article->expose;
    else $this->expose = $this->section->expose;
  }

  public function getLoi($request, $light = 0) {
    $loi_id = $request->getParameter('loi');
    if ($light)
      $this->loi = doctrine::getTable('TitreLoi')->findLightLoi($loi_id);
    else $this->loi = doctrine::getTable('TitreLoi')->findLoi($loi_id);
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

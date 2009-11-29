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
      ->andWhere('t.date IS NULL')
      ->orderBy('t.chapitre, t.section')
      ->execute();
    if (!$this->soussections) {
      $this->articles = doctrine::getTable('ArticleLoi')->createquery('a')
        ->where('a.texteloi_id = ?', $loi_id)
        ->orderBy('a.numero')
        ->execute();
    }
    $this->response->setTitle($this->loi->titre);
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
      ->orderBy('a.numero');
    $this->articles = $artquery->execute();
    if (count($this->articles) == 1)
      $this->redirect('@loi_article?loi='.$loi_id.'&article='.$this->articles[0]->numero);
    if (isset($this->section)) {
      $titre = $this->section->getLargeTitre();
      if (doctrine::getTable('TitreLoi')->findSection($loi_id, $n_chapitre, $n_section+1))
        $this->suivant = $n_section + 1;
    } else {
      $titre = $this->chapitre->getLargeTitre();
      if (doctrine::getTable('TitreLoi')->findChapitre($loi_id, $n_chapitre+1))
        $this->suivant = $n_chapitre + 1; 
    }
    $this->response->setTitle($this->loi->titre.' - '.strip_tags($titre));
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
    $this->n_article = $article->numero;
    $this->article_id = $article->id;
    $this->titre = 'Article '.$this->n_article.' - Alinea '.$this->alinea->numero;
    $this->response->setTitle($this->titre);
  }



  public function executeArticle(sfWebRequest $request) {
    if ($id = $request->getParameter('id'))
      $this->article = doctrine::getTable('ArticleLoi')->find($id);
    else {
      $loi_id = $this->getLoi($request, 1);
      $this->n_article = $request->getParameter('article');
      $this->article = doctrine::getTable('ArticleLoi')->findOneByLoiNum($loi_id, $this->n_article);
    }
    $this->forward404Unless($this->article);
    if (!(isset($this->n_article))) {
      $this->n_article = $this->article->numero;
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
    $this->titre = 'Article '.$this->n_article;
    if (isset($this->section->chapitre) && $this->section->chapitre != 0)
      $this->titre .= ' ('.$this->section->getLargeTitre().')';
    $this->response->setTitle($this->loi->titre.' - '.strip_tags($this->titre));
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

}

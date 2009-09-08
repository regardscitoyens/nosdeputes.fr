<?php

/**
 * article actions.
 *
 * @package    cpc
 * @subpackage article
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class articleActions extends sfActions
{
  public function processArticle(sfWebRequest $request, $new = 0) {
    $categorie = $request->getParameter('categorie');
    $object_id = $request->getParameter('object_id');
    $object = null;

    if ($object_id) {
      $object = Doctrine::getTable($categorie)->find($object_id);
    }

    $this->form->setParent($request->getParameter('hasParent', false), $categorie);
    $this->form->setObject($request->getParameter('hasObject', false), $categorie, $request->getParameter('displayObject', true));
    $this->form->setTitre($request->getParameter('hasTitre', true));

    if (($t = $request->getParameter('autoObjectTitre')) && $object) {
      $this->titre = $t.$object;
    }

    if ($request->isMethod('post')) {
      $farticle = $request->getParameter('article', array() );
      $farticle['categorie'] = $categorie;
      if ($object_id)
	$farticle['object_id'] = $object_id;
      if ($this->titre)
	$farticle['titre'] = $this->titre;
      if (isset($farticle['user_corps']))
	$farticle['corps'] = myTools::clearHtml($farticle['user_corps']);
      $this->form->setUnique($request->getParameter('isUnique', false));
      $this->form->bind($farticle);
    }
    $this->article = $this->form->getValue('corps');
    if (!$request->isMethod('post'))
	return ;
    $this->post = 1;
    if (!$this->form->isValid()) {
      return ;
    }
    if (!$request->getParameter('ok'))
      return ;
    $this->form->save();
    return $this->redirect('faq');
  }
  public function executeUpdate(sfWebRequest $request)
  {
    $article = Doctrine::getTable('Article')->find($request->getParameter('article_id'));
    $this->form = new ArticleForm($article);
    $this->processArticle($request);
  }
  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new ArticleForm();
    $this->setTemplate('update');
    $this->processArticle($request, 1);
  }
  public function executeDelete(sfWebRequest $request) 
  {
    $this->article = Doctrine::getTable('Article')->find($request->getParameter('article_id'));
    $this->forward404Unless($this->article);

    if (!$request->isMethod('post'))
      return;
    if (!$request->getParameter('confirm'))
      return;
    $this->article->delete();
    return $this->redirect('faq');
  }
  public function executeList(sfWebRequest $request)
  {
    $this->articles = Doctrine::getTable('Article')->createQuery('a')
      ->where('categorie = ?', $request->getParameter('categorie'))
      ->andWhere('article_id IS NULL')->execute();

    $this->sousarticles = array();
    foreach($this->articles as $a) {
      $this->sousarticles[$a->id] = Doctrine::getTable('Article')->createQuery('a')
	->where('categorie = ?', $request->getParameter('categorie'))
	->andWhere('article_id = ?', $a->id)->execute();
    }
    $this->titre = $request->getParameter('titre');
  }

  public function executeFindSeance(sfWebRequest $request)
  {
    $this->form = new DateForm();
    $value = $request->getParameter('date');
    if ($value) {
      $this->form->bind($value);
    }
    $this->section = $request->getParameter('section');

    $this->section_id = $request->getParameter('section_id');
    $this->cdate = $request->getParameter('cdate');

    if ($this->section) {
      $this->found_sections = Doctrine::getTable('Section')->createQuery('s')->where('s.id = s.section_id')->andWhere('titre LIKE ?', '%'.$this->section.'%')->fetchArray();
    }else if ($value && $this->form->isValid()) {
      $this->found_sections = Doctrine::getTable('Section')->createQuery('s')->where('s.id = s.section_id')->leftJoin('s.Interventions i')->andWhere('i.date = ?', $this->form->getValue('date'))->groupBy('s.id')->fetchArray();
    }
    
    $section_id = $request->getParameter('section_id');
    if ($section_id) {
      $qseances = Doctrine::getTable('Seance')->createQuery('s')->leftJoin('s.Interventions i')->where('i.section_id = ?', $section_id);
      $cdate = $request->getParameter('cdate');
      if ($cdate)
	$qseances->andWhere('s.date = ?', $cdate);
      $this->seances = $qseances->fetchArray();
      if (count($this->seances) == 1)
	return $this->redirect('@compterendu_new?object_id='.$this->seances[0]['id']);
    }
  }
}

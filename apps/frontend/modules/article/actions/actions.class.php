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
    $object = $this->form->getObject();
    if (!$object->citoyen_id) {
      $object->citoyen_id = $this->getUser()->getAttribute('user_id');
      $object->save();
    }

    $this->getUser()->setFlash('notice', 'Votre article a été enregistré');
    if ($l = $request->getParameter('link')) {
      $slug = 'toto';
      if ($object->citoyen_id)
	$slug = $object->getCitoyen()->slug;
      $object->link = sprintf($l, $object->slug, $slug);
      $object->save();
    }
    if ($object->link)
      return $this->redirect($object->link);
    return $this->redirect("@home");
  }

  public function protect($request, $user_id = '') {
    $role = $request->getParameter('role', -1);
    if ($role == -1)
      throw new Exception('for security reason, you should affect role parameter in routing.yml');
    $exclude = $request->getParameter('exclude', -1);
    if ($exclude == -1)
      throw new Exception('for security reason, you should affect exclude parameter in routing.yml');

    if (!$this->getUser()->isAuthenticated()) {
      $this->getUser()->setFlash('notice', 'Vous devez être identifié pour avoir accès à cette page');
      $this->getResponse()->setStatusCode(401);
      return $this->forward('citoyen', 'signin');
    }
    if (!$this->getUser()->hasCredential('membre')) {
      $_GET['moderateur'] = 1;
    }
    if ($user_id == $this->getUser()->getAttribute('user_id'))
      return ;
    if ($role) {
      if ($exclude && $this->getUser()->hasCredential($role))
	return $this->forward('citoyen', 'notauthorized');
      if (!$exclude && !$this->getUser()->hasCredential($role))
	return $this->forward('citoyen', 'notauthorized');
    }
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $article = Doctrine::getTable('Article')->find($request->getParameter('article_id'));
    $this->forward404Unless($article);
    $this->protect($request, $article->citoyen_id);
    $this->form = new ArticleForm($article);
    $this->processArticle($request);
  }
  public function executeCreate(sfWebRequest $request)
  {
    $this->protect($request);
    $this->form = new ArticleForm();
    $this->setTemplate('update');
    $this->processArticle($request, 1);
  }
  public function executeDelete(sfWebRequest $request) 
  {
    $this->article = Doctrine::getTable('Article')->find($request->getParameter('article_id'));
    $this->forward404Unless($this->article);

    $this->protect($request, $this->article->citoyen_id);
    if (!$request->isMethod('post'))
      return;
    if (!$request->getParameter('confirm'))
      return $this->redirect($this->article->link);
    $this->getUser()->setFlash('notice', 'Votre article a été supprimé');
    $id = $this->article->id;
    Doctrine_Query::create()->update('Article')->set('article_id', 'NULL')->where('article_id = '.$id)->execute();
    $this->article->delete();
    return $this->redirect('@homepage');
  }
  public function executeList(sfWebRequest $request)
  {
    $this->articles = Doctrine::getTable('Article')->createQuery('a')
      ->where('categorie = ?', $request->getParameter('categorie'))
      ->andWhere('article_id IS NULL')
      ->andWhere('status = ?', 'public')
      ->execute();

    $this->sousarticles = array();
    foreach($this->articles as $a) {
      $this->sousarticles[$a->id] = Doctrine::getTable('Article')->createQuery('a')
	->where('categorie = ?', $request->getParameter('categorie'))
	->andWhere('status = ?', 'public')
	->andWhere('article_id = ?', $a->id)->execute();
    }
    $this->titre = $request->getParameter('titre');
    $this->response->setTitle($this->titre);
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
  public function executePager(sfWebRequest $request)
  {
    $categorie = $request->getParameter('categorie');
    $qarticles = Doctrine::getTable('Article')->createQuery('a')
      ->where('a.categorie = ?', $categorie)
      ->andWhere('status = ?', 'public')
      ->orderBy('a.created_at DESC');
    $pager = new sfDoctrinePager('Article',20);
    $pager->setQuery($qarticles);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
    $this->titre = $request->getParameter('titre', 'Il manque un titre dans le routing');
  }

  public function executeShow(sfWebRequest $request) 
  {
    $categorie = $request->getParameter('categorie');
    $this->article = Doctrine::getTable('Article')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->article);
    $this->forward404Unless($this->article->status == 'public');
    $this->forward404Unless($this->article->categorie == $categorie);
  }
}

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

    $this->form->setParent($request->getParameter('hasParent', false), $categorie);
    $this->form->setObject($request->getParameter('hasObject', false), $categorie);
    $this->form->setTitre($request->getParameter('hasTitre', true));

    if ($request->isMethod('post')) {
      $farticle = $request->getParameter('article', array() );
      $farticle['categorie'] = $categorie;
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
}

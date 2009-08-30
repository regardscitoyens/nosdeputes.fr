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
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new ArticleForm();
    $farticle = $request->getParameter('article');
    $farticle['categorie'] = $request->getParameter('categorie');
    $this->form->bind($farticle);
    $this->form->setParent($request->getParameter('hasParent', false));
    $this->form->setObject($request->getParameter('hasObject', false));
    if (!$request->isMethod('post'))
	return ;
    if (!$request->getParameter('ok'))
      return ;
    $this->form->save();
    return $this->redirect('faq_new');
  }
}

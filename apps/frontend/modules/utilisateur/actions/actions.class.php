<?php

/**
 * utilisateur actions.
 *
 * @package    cpc
 * @subpackage utilisateur
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class utilisateurActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->utilisateur_list = Doctrine::getTable('Utilisateur')
      ->createQuery('a')
      ->execute();
		$response = $this->getResponse();
		$response->setTitle('Liste des utilisateurs'); 
  }
	
	public function executeNew(sfWebRequest $request)
  {
    $this->form = new UtilisateurForm();
		$response = $this->getResponse();
		$response->setTitle('Inscription'); 
  }
	
	public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new UtilisateurForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }
	
	public function executeEdit(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
		$this->forward404Unless($utilisateur = Doctrine::getTable('Utilisateur')->findOneBySlug($slug));
    $this->form = new UtilisateurForm($utilisateur);
		$response = $this->getResponse();
		$response->setTitle('Edition de votre profil'); 
  }
	
	public function executeUpdate(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
		$this->forward404Unless($utilisateur = Doctrine::getTable('Utilisateur')->findOneBySlug($slug));
    $this->form = new UtilisateurForm($utilisateur);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
		$response = $this->getResponse();
		$response->setTitle('Edition de votre profil'); 
  }
	
	public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $slug = $request->getParameter('slug');
		$this->forward404Unless($utilisateur = Doctrine::getTable('Utilisateur')->findOneBySlug($slug));
    $utilisateur->delete();

    $this->redirect('utilisateur');
  }
	
	protected function processForm(sfWebRequest $request, sfForm $form)
  {
		$id = $request->getParameter('id');
		$login = $request->getParameter('login');
		$pass = $request->getParameter('pass');
		$sexe = $request->getParameter('sexe');
		$naissance = $request->getParameter('naissance');
		$profession = $request->getParameter('profession');
		$parlementaire_id = $request->getParameter('parlementaire_id');
		$mail = $request->getParameter('mail');
		$activation_id = $request->getParameter('activation_id');
		$activation = $request->getParameter('activation');
		$circo = $request->getParameter('circo');
		$circo_num = $request->getParameter('circo_num');
		$photo = $request->getParameter('photo');
		$created_at = $request->getParameter('created_at');
		$updated_at = $request->getParameter('updated_at');
		$slug = $request->getParameter('slug');
		
		$form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $utilisateur = $form->save();

      $this->redirect('utilisateur/'.$utilisateur->getSlug());
    }
  }
	
	public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->Utilisateur = Doctrine::getTable('Utilisateur')->findOneBySlug($slug);
		$response = $this->getResponse();
		$response->setTitle('Mini blog de '.$this->Utilisateur->login); 
  }
	
  public function executeList(sfWebRequest $request)
  {
    
  }

}
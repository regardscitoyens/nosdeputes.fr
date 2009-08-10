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
    $form = new UtilisateurForm();
    
    if ($request->isMethod('post') || $request->isMethod('put')) // If HTTP method is POST
    {
      $form->bind($request->getParameter('utilisateur'));
      $this->executeCreate($request, $this->form);
    }
    // Publish form instance to the view
    $this->form = $form;
    // le titre
    $response = $this->getResponse();
    $response->setTitle('Formulaire d\'inscription'); 
  }
  
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));

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
    $response->setTitle('Update de votre profil'); 
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $slug = $request->getParameter('slug');
    $this->forward404Unless($utilisateur = Doctrine::getTable('Utilisateur')->findOneBySlug($slug));
    $utilisateur->delete();

    $this->redirect('@homepage');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    // Bind submitted values to the inscription form instance
    $form->bind($request->getParameter('utilisateur'));
      
    // Validate the form
    if ($form->isValid()) 
    {
      // on récupère les valeurs
      $values = $this->form->getValues();
      // pour inserer les autres
      $id = '';  
      $naissance= $values['naissance']['year'] .'-'. $values['naissance']['month'].'-'.$values['naissance']['day'];
      $activation_id = time();  
      $activation = false;
      $slug = strtolower($values['login']);
      $slug = preg_replace("/ /", "-", $slug);
      $created_at = '';
      $updated_at = '';
      
      $request->setAttribute('utilisateur["id"]', $id);
      $request->setAttribute('utilisateur["naissance"]', $naissance);
      $request->setAttribute('utilisateur["activation_id"]', $activation_id);
      $request->setAttribute('utilisateur["activation"]', $activation);
      $request->setAttribute('utilisateur["slug"]', $slug);
      $request->setAttribute('utilisateur["created_at"]', $created_at);
      $request->setAttribute('utilisateur["updated_at"]', $updated_at);
      
      // on sauve
      $form = $this->form->save();
      // message
      $this->getUser()->setFlash('notice', 'Création de votre profil ok');
      echo $form;
      $this->redirect('@list_utilisateurs');
      #$this->redirect("/citoyen/:$slug");
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
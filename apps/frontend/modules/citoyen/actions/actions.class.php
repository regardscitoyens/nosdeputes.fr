<?php

/**
 * citoyen actions.
 *
 * @package    cpc
 * @subpackage citoyen
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class citoyenActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
	public function executeIndex(sfWebRequest $request)
  {
    $this->citoyens_list = Doctrine::getTable('Citoyen')
      ->createQuery('a')
      ->execute();
    $response = $this->getResponse();
    $response->setTitle('Liste des citoyens inscrits'); 
  }
	
	public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->Citoyen = Doctrine::getTable('Citoyen')->findOneBySlug($slug);
    $response = $this->getResponse();
    $response->setTitle('Mini blog de '.$this->Citoyen->username); 
  }
	
  public function executeNew(sfWebRequest $request)
  {
    if (!$this->getUser()->isAuthenticated()) {
			$this->form = new InscriptionForm();
			if ($request->isMethod('post'))
			{
				$this->form->bind($request->getParameter('sf_guard_user'));
				
				if ($this->form->isValid())
				{
					$this->processForm($request, $this->form);
				}  
			}
		}
		else
		{
			$user = $this->getUser()->getGuardUser();
			$this->getUser()->setFlash('notice', 'Vous etes deja inscrit');
			$this->redirect('@citoyen?slug='.$user->Citoyen->slug);
		}
  }
	
	public function executeEdit(sfWebRequest $request)
  {
		if ($this->getUser()->isAuthenticated()) {
		$user = $this->getUser()->getGuardUser();
		
			$this->form = new EditUserForm($user);
			
			if ($request->isMethod('put'))
			{
				$this->form->bind($request->getParameter('sf_guard_user'));
				
				if ($this->form->isValid())
				{
					$this->form->save();
					$this->getUser()->setFlash('notice', 'Votre modification a reussi');
					$this->redirect('@citoyen?slug='.$user->Citoyen->slug);
				}  
			}
		}
		else { $this->redirect('@list_citoyens'); } 
  }
	
	public function executeActivation(sfWebRequest $request)
  {
    $activation_id = $request->getParameter('activation_id');
		if (Doctrine::getTable('Citoyen')->findOneByActivationId($activation_id))
		{
			$this->user_activation_id = Doctrine::getTable('Citoyen')->findOneByActivationId($activation_id);
			#$slug = $this->user_activation_id->getSlug;
			$this->user_edit = Doctrine::getTable('sfGuardUser')->findOneById($this->user_activation_id->getsfGuardUserId());
			if (!$this->user_edit->getIsActive())
			{
				$this->user_edit->setIsActive(true);
				$this->user_edit->addGroupByName('membre');
				$this->user_edit->save();
				$this->getUser()->setFlash('notice', 'Votre compte a ete active avec succes');
				$this->redirect('@activation_citoyen');
			}
			else
			{
				$this->getUser()->setFlash('notice', 'Ce compte est deja active');
				$this->redirect('@activation_citoyen');
			}
		}
		else
		{
			$this->redirect('@list_citoyens');
			$this->getUser()->setFlash('notice', 'Ce compte n\'existe pas');
		}
  }
	
	public function executeDelete()
  {
    
  }
	
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $values = $request->getParameter('sf_guard_user');
		
    $user = new sfGuardUser();       
    $user->setUsername($values["username"]);
    $user->setPassword($values["password"]);
    #$user->addGroupByName('membre');
    $user->save();
    
    $user_id = Doctrine::getTable('sfGuardUser')->findOneByUsername($values["username"]);
    $id = $user_id->getId();
    $activation_id = md5(time());

    $citoyen = new Citoyen();
    $citoyen->setsfGuardUserId($id);
    $citoyen->setUsername($values["username"]);
    $citoyen->setEmail($values["Citoyen"]["email"]);
    if (!empty($values["Citoyen"]["profession"])) {
      $citoyen->setProfession($values["Citoyen"]["profession"]);
    }
    if (!empty($values["Citoyen"]["naissance"]["year"])) {
      $naissance= $values["Citoyen"]["naissance"]["year"] .'-'. $values["Citoyen"]["naissance"]["month"].'-'.$values["Citoyen"]["naissance"]["day"];
      $citoyen->setNaissance($naissance);
    }
    if (!empty($values["Citoyen"]["sexe"])) {
      $citoyen->setSexe($values["Citoyen"]["sexe"]);
    }
    $citoyen->setActivationId($activation_id);
    $citoyen->save();
		$slug = $citoyen->getSlug();

    $this->getUser()->signIn($user);
    $this->getUser()->setFlash('notice', 'Un email de confirmation vient de vous etre envoye');
    $this->redirect('@citoyen?slug='.$slug);
  }

}
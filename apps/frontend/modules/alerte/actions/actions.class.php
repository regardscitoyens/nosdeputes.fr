<?php

/**
 * alerte actions.
 *
 * @package    cpc
 * @subpackage alerte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class alerteActions extends sfActions
{
  public function executeParlementaire(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($slug);
    $parlementaire = doctrine::getTable('Parlementaire')->findBySlug($slug);
    $this->forward404Unless($parlementaire);    
  }

  public function executeList(sfWebRequest $request) {
    $citoyen_id = $this->getUser()->getAttribute('user_id');
    $this->forward404Unless($citoyen_id);
    $citoyen = doctrine::getTable('Citoyen')->find($citoyen_id);
    $this->forward404Unless($citoyen);
    $sql = doctrine::getTable('Alerte')->createQuery('a')->where('a.citoyen_id = ?', $citoyen_id);//->orWhere('a.email = ?', $citoyen->getEmail());
    $this->alertes = $sql->execute();
  }


  public function executeCreate(sfWebRequest $request) 
  {
    $alerte = new Alerte();
    if ($citoyen_id = $this->getUser()->getAttribute('user_id')) {
      $alerte->citoyen_id = $citoyen_id;
    }
    $this->form = new AlerteForm($alerte);
    $this->submit = 'Créer';
    $this->processForm($request, $this->form);
    $this->setTemplate('form');
  }
  public function executeDelete(sfWebRequest $request) 
  {
    $this->forward404Unless($this->alerte = Doctrine::getTable('Alerte')->createQuery('a')->where('verif = ?', $request->getParameter('verif'))->fetchOne());
    if ($request->isMethod('post')) {
      if ($request->getParameter('confirmed')) {
	$this->alerte->delete();
	$this->getUser()->setFlash('notice', 'Votre alerte email a bien été supprimée');
      }else
	$this->getUser()->setFlash('error', 'Votre alerte email n\'a pas été supprimée');
      return $this->redirectPostSave();
    }
  }

  public function executeEdit(sfWebRequest $request) 
  {
    $this->forward404Unless($alerte = Doctrine::getTable('Alerte')->createQuery('a')->where('verif = ?', $request->getParameter('verif'))->fetchOne());
    $this->form = new AlerteForm($alerte);
    $this->processForm($request, $this->form);
    $this->submit = 'Éditer';
    $this->setTemplate('form');
  }

  private function redirectPostSave($alerte = null) {
    if ($citoyen_id = $this->getUser()->getAttribute('user_id'))
      return $this->redirect('alerte/list');
    else if ($alerte_id)
      return $this->redirect('alerte/edit?id='.$alerte_id);
    else
      return $this->redirect('@homepage');
  }

  private function processForm($request, $form) {
    if ($request->isMethod('post')) {
      $form->bind($request->getParameter($form->getName()));
      if ($form->isValid()) {
	$form->save();
	if ($this->submit == 'Créer') {
	  $this->getUser()->setFlash('notice', 'Votre alerte email a été créée');
	}else {
	  $this->getUser()->setFlash('notice', 'Votre alerte email a été modifiée');
	}
	return $this->redirectPostSave($form->getObject()->id);
      }
    }
  }
}

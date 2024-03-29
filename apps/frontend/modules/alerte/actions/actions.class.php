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
    $parlementaire = doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($parlementaire);
    $alerte = new Alerte();
    $alerte->query = '"Parlementaire='.$parlementaire.'"';
    $alerte->no_human_query = 1;
    $alerte->titre = 'Recherche relative aux travaux de '.$parlementaire->nom;
    $this->submit = 'Créer';
    $this->form = $this->processForm($request, $alerte);
    $this->setTemplate('form');
    myTools::setPageTitle("Suivre l'activité parlementaire de".$parlementaire->nom." par e-mail", $this->response);
  }

  public function executeQuestion(sfWebRequest $request)
  {
    $num = $request->getParameter('num');
    $this->forward404Unless($num);
    $question = doctrine::getTable('QuestionEcrite')->findOneByNumero($num);
    $this->forward404Unless($question);
    $alerte = new Alerte();
    $alerte->query = 'object_id:'.$question->id.' object_name:QuestionEcrite';
    $alerte->no_human_query = 1;
    $alerte->titre = 'Changements liés à la Question Écrite N° '.$question->numero;
    $this->submit = 'Créer';
    $this->form = $this->processForm($request, $alerte);
    $this->setTemplate('form');
    myTools::setPageTitle("Suivre les changements liés à la Question Écrite N° ".$question->numero." par e-mail", $this->response);
  }

  public function executeList(sfWebRequest $request) {
    $citoyen_id = $this->getUser()->getAttribute('user_id');
    if ($citoyen_id)
      $citoyen = doctrine::getTable('Citoyen')->find($citoyen_id);
    if (isset($citoyen) && $citoyen) {
      $sql = doctrine::getTable('Alerte')->createQuery('a')->where('a.citoyen_id = ?', $citoyen_id)->orWhere('a.email = ?', $citoyen->getEmail());
      $this->alertes = $sql->execute();
    }
  }


  public function executeCreate(sfWebRequest $request)
  {
    $alerte = new Alerte();
    $alerte->query = myTools::escapeHtml($request->getParameter('query'));
    $alerte->filter = myTools::escapeHtml($request->getParameter('filter'));
    $this->submit = 'Créer';
    $this->form = $this->processForm($request, $alerte);
    $this->setTemplate('form');
    myTools::setPageTitle("Créer une alerte e-mail sur des travaux de l'Assemblée nationale", $this->response, false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($this->alerte = Doctrine::getTable('Alerte')->createQuery('a')->where('verif = ?', $request->getParameter('verif'))->fetchOne());
    if ($request->isMethod('post')) {
      if ($request->getParameter('confirmed')) {
	$this->alerte->delete();
	$this->getUser()->setFlash('notice', 'Votre alerte e-mail a bien été supprimée');
      }else
	$this->getUser()->setFlash('error', 'Votre alerte e-mail n\'a pas été supprimée');
      return $this->redirectPostSave();
    }
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($alerte = Doctrine::getTable('Alerte')->createQuery('a')->where('verif = ?', $request->getParameter('verif'))->fetchOne());
    $this->form =  $this->processForm($request, $alerte);
    $this->submit = 'Éditer';
    $this->setTemplate('form');
    $this->response->setTitle("Modification d'une alerte e-mail");
  }

/**
  * Administrative task : remove exit when needed
  */
  public function executeResend(sfWebRequest $request)
  {
    $this->forward404Unless($alerte = Doctrine::getTable('Alerte')->createQuery('a')->where('verif = ?', $request->getParameter('verif'))->fetchOne());
    exit;
    $this->confirmeAlerte($alerte);
    $this->getUser()->setFlash('notice', 'Alerte réenvoyée');
    return $this->redirect("@homepage");
  }

  public function executeConfirmation(sfWebRequest $request)
  {
    $this->forward404Unless($alerte = Doctrine::getTable('Alerte')->createQuery('a')->where('verif = ?', $request->getParameter('verif'))->fetchOne());
    $alerte->confirmed = 1;
    $alerte->save();
    $this->getUser()->setFlash('notice', 'Merci d\'avoir confirmé votre alerte');
    return $this->redirect("@homepage");
  }
  private function redirectPostSave($alerte = null) {
    if ($citoyen_id = $this->getUser()->getAttribute('user_id'))
      return $this->redirect('alerte/list');
    else if ($alerte)
      return $this->redirect('alerte/edit?verif='.$alerte->verif);
    else
      return $this->redirect('@homepage');
  }

  private function processForm($request, $alerte) {
    if ($citoyen_id = $this->getUser()->getAttribute('user_id')) {
      $alerte->citoyen_id = $citoyen_id;
      if (!$alerte->getCitoyen()->is_active) {
	$this->getUser()->setFlash('error', "Votre compte n'a pas été confirmé, vous ne pouvez donc pas créer d'alerte");
	return $this->redirect('@homepage');
      }
    }
    $form = new AlerteForm($alerte);
    if ($request->isMethod('post')) {
      $form->bind($request->getParameter($form->getName()));
      if ($form->isValid()) {
	try {
	  if (!$form->save()) {
	    throw new Exception();
	  }
	}catch(Exception $e) {
	  $this->getUser()->setFlash('error', 'Désolé nous n\'avons pu créer votre alerte, vous y étiez sans doute déjà abonné');
	  return $this->redirect('@homepage');
	}
	if ($this->submit == 'Créer') {
	  if ($alerte->confirmed)
	    $this->getUser()->setFlash('notice', 'Votre alerte e-mail a été créée');
	  else {
	    $this->confirmeAlerte($alerte);
	    $this->getUser()->setFlash('notice', 'Votre alerte e-mail a été créée, merci de confirmer votre abonnement par e-mail');
	  }
	}else {
	  $this->getUser()->setFlash('notice', 'Votre alerte e-mail a été modifiée');
	}
	return $this->redirectPostSave($form->getObject());
      }
    }
    return $form;
  }
  private function confirmeAlerte($alerte) {
    $message = $this->getMailer()->compose(array('nosdeputes@nosdeputes.fr' => '"Regards Citoyens"'),
					   $alerte->email,
					   '[NosDeputes.fr] Confirmation d\'Alerte e-mail - '.$alerte->titre);
    $text = $this->getPartial('mail/sendConfirmationAlerte', array('alerte' => $alerte));
    $message->setBody($text, 'text/plain');
    try {
      $this->getMailer()->send($message);
    }catch(Exception $e) {
      echo "Oups could not send e-mail ($text)\n";
    }
  }
}

<?php

/**
 * intervention actions.
 *
 * @package    cpc
 * @subpackage loi
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class documentsActions extends sfActions
{


  public function executeShow(sfWebRequest $request) {
   $id = $request->getParameter('id');
   if ($loi = Doctrine::getTable('Titreloi')->findLightLoi("$id"))
     $this->redirect('@loi?loi='.$id);
   $this->doc = Doctrine::getTable('Texteloi')->find("$id");
   if (!$this->doc)
     $this->doc = Doctrine::getTable('Texteloi')->createQuery('t')
       ->where('numero = ?', $id)
       ->andWhere('annexe = 1')
       ->fetchOne();
   $this->forward404Unless($this->doc);

   $this->auteurs = $this->doc->getAuteurs();
   $this->cosign = $this->doc->getCosignataires();
   $this->qtag = $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t')
      ->where('tg.taggable_id = ?', $this->doc->id);
   $this->section = $this->doc->getSection();
   $this->amendements = $this->doc->getAmendements();
   $this->orga = $this->doc->getCommission();
   if (preg_match('/^(\d+)-[at]/', $id, $match))
     $this->texte = count(Doctrine::getTable('Texteloi')->find($match[1]));
   $this->annexes = Doctrine::getTable('Texteloi')->createQuery('t')
     ->select('id, annexe')
     ->where('numero = ?', $this->doc->numero)
     ->andWhere('annexe is not null')
     ->orderBy('annexe')
     ->fetchArray();
   $this->response->setTitle($this->doc->getTitre().' - NosDéputés.fr');

  }

}

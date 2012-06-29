<?php

/**
 * documents actions.
 *
 * @package    cpc
 * @subpackage documents
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
   if (!$this->doc && preg_match('/(\d+)/', $id, $match))
     $this->doc = Doctrine::getTable('Texteloi')->createQuery('t')
       ->where('numero = ?', $match[1])
       ->orderBy('annexe')
       ->fetchOne();
   $this->forward404Unless($this->doc);

   $this->auteurs = $this->doc->getAuteurs();
   $this->cosign = $this->doc->getCosignataires();
   $this->qtag = $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t')
      ->where('tg.taggable_id = ?', $this->doc->id);
   $this->section = $this->doc->getSection();
   $this->amendements = $this->doc->getAmendements(1);
   $this->orga = $this->doc->getCommission();
   if (preg_match('/^(\d+)-[at]/', $id, $match))
     $this->texte = count(Doctrine::getTable('Texteloi')->find($match[1]));
   $this->annexes = Doctrine::getTable('Texteloi')->createQuery('t')
     ->select('id, annexe')
     ->where('numero = ?', $this->doc->numero)
     ->andWhere('annexe is not null')
     ->orderBy('annexe')
     ->fetchArray();
   $this->relatifs = Doctrine_Query::create()
     ->select('t.id, t.type, t.type_details, t.titre, t.signataires')
     ->from('Texteloi t')
     ->where('t.id_dossier_an = ?', $this->doc->id_dossier_an)
     ->andWhere('t.id <> ?', $this->doc->id)
     ->orderBy('t.numero, t.annexe')
     ->fetchArray();
   $this->response->setTitle($this->doc->getTitre().' - NosDéputés.fr');
  }

  public function executeParlementaire(sfWebRequest $request) {
    $this->type = $request->getParameter('type');
    $this->forward404Unless(preg_match('/^(loi|rap)$/', $this->type));
    $this->parlementaire = Doctrine::getTable('Parlementaire')
      ->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    $this->typetitre = "rapports";
    $this->feminin = "";
    if ($this->type === "loi") {
      $this->typetitre = "propositions de loi";
      $this->feminin = "e";
    }
    $this->docs = Doctrine::getTable('Texteloi')->createQuery('t')
      ->select('t.*, p.fonction as fonction')
      ->leftJoin('t.ParlementaireTexteloi p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id);
    $lois = array('Proposition de loi', 'Proposition de résolution');
    if ($this->type === "loi")
      $this->docs->andWhere('(t.type = ? OR t.type = ?)', $lois);
    else if ($this->type === "rap")
      $this->docs->andWhere('t.type != ? AND t.type != ?', $lois);
    $this->docs->orderBy('t.date DESC');
  
    $this->response->setTitle('Les '.$this->typetitre.' de '.$this->parlementaire->nom.' - NosDéputés.fr');
//    $request->setParameter('rss', array(array('link' => '@parlementaire_documents_rss?slug='.$this->parlementaire->slug.'&type='.$this->type, 'title'=>'Les dernier'.$this->feminin.'s '.$this->typetitre.' de '.$this->parlementaire->nom.' en RSS')));
    $request->setParameter('rss', array(array('link' => '@parlementaire_documents_rss?slug='.$this->parlementaire->slug.'&type='.$this->type, 'title'=>'Les derniers documents parlementaires de '.$this->parlementaire->nom.' en RSS')));
  }
}

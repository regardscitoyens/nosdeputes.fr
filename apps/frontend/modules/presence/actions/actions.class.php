<?php

/**
 * parlementaire actions.
 *
 * @package    cpc
 * @subpackage parlementaire
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class presenceActions extends sfActions
{

  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    if ($this->type = $request->getParameter('type'))
      $this->forward404Unless(preg_match('/(hemicycle|commission)/', $this->type));
    else $this->type = "all";
    $query = Doctrine::getTable('Presence')->createQuery('p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('p.Seance s')
      ->leftJoin('s.Organisme o')
      ->leftJoin('p.Preuves pr')
      ->orderBy('s.date DESC, s.type ASC, s.moment ASC')
      ->groupBy('p.Seance.id');
    if ($this->type != "all")
      $query->andWhere('s.type = ?', $this->type);
    $this->presences = $query->execute();

    $this->titre = "Présences en ";
    if ($this->type == "all") {
      $this->titre = "Prises de parole en hémicycle et présences en commissions";
    }elseif($this->type == "hemicycle") {
      $this->titre = "Prises de parole en hémicycle";
    }else{
      $this->titre .= $this->type;
    }
    myTools::setPageTitle($this->titre." de ".$this->parlementaire->nom, $this->response);
  }

  public function executePreuve(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    $seance_id = $request->getParameter('seance');
    $this->seance = Doctrine::getTable('Seance')->findOneById($seance_id);
    $this->forward404Unless($this->seance);
    $this->preuves = Doctrine::getTable('PreuvePresence')
      ->createQuery('pp')
      ->leftJoin('pp.Presence p')
      ->where('p.seance_id = ?', $seance_id)
      ->andWhere('p.parlementaire_id = ?', $this->parlementaire->id)
      ->execute();
    $this->titre = 'Preuve de présence de '.$this->parlementaire->nom.' à la '.$this->seance->getTitre(1);
    myTools::setPageTitle($this->titre, $this->response, false);
  }

  public function executeSeance(sfWebRequest $request)
  {
    $seance_id = $request->getParameter('seance');
    $this->forward404Unless($seance_id);
    $this->seance = Doctrine::getTable('Seance')->createQuery('s')
      ->where('s.id = ?', $seance_id)
      ->leftJoin('s.Organisme')
      ->fetchOne();
    $this->forward404Unless($this->seance);
    $this->intervenants = Doctrine::getTable('Presence')->createQuery('p')
      ->leftJoin('p.Parlementaire pa')
      ->leftJoin('p.Preuves pr')
      ->where('p.seance_id = ?', $seance_id)
      ->andWhereIn('pr.type', array('intervention', 'video'))
      ->groupBy('pa.id')
      ->orderBy('pa.nom_de_famille')
      ->execute();
    $this->presents = Doctrine::getTable('Presence')->createQuery('p')
      ->where('p.seance_id = ?', $seance_id)
      ->leftJoin('p.Parlementaire pa')
      ->leftJoin('p.Preuves pr')
      ->groupBy('pa.id')
      ->orderBy('pa.nom_de_famille ASC')
      ->execute();
    $this->orga = $this->seance->getOrganisme();
    myTools::setPageTitle(($this->orga !== null ? $this->orga->getNom() : "Hémicycle").' - Députés présents à la '.$this->seance->getTitre(1), $this->response);
  }
}

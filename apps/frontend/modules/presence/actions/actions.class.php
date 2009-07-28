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
  public function executeTop(sfWebRequest $request)
  {
    $q = Doctrine_Query::create()
      ->select('p.*, count(pr.id) as nb')
      ->from('Parlementaire p')
      ->leftJoin('p.Presences pr')
      ->groupBy('p.id')
      ->orderBy('nb DESC');
    $this->top = $q->fetchArray();
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($this->parlementaire);

    $query = Doctrine::getTable('Presence')->createQuery('p');
    $query->where('p.parlementaire_id = ?', $this->parlementaire->id);
    $query->leftJoin('p.Seance.Organisme o')
      ->leftJoin('p.Preuves pr')
      ->orderBy('p.Seance.type ASC, p.Seance.date DESC')
      ->groupBy('p.Seance.id');
    $this->presences = $query->execute();
    $this->forward404Unless($this->presences);
  }

  public function executePlotParlementaire(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($this->parlementaire);
    $query = Doctrine_Query::create()
        ->select('COUNT(*) as nombre, p.*')
        ->from('Presence p')
        ->where('p.parlementaire_id = ?', $this->parlementaire->id)
        ->leftJoin('p.Seance s')
        ->addSelect('s.annee, s.numero_semaine')
        ->orderBy('s.annee DESC')
        ->addOrderBy('s.numero_semaine DESC')
        ->groupBy('s.annee')
        ->addGroupBy('s.numero_semaine');
    $this->presences = $query->fetchArray();
    $this->forward404Unless($this->presences);
  }

  public function executePreuve(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($parlementaire);
    $seance_id = $request->getParameter('seance');
    $this->preuves = doctrine::getTable('PreuvePresence')->createQuery('pp')->leftJoin('pp.Presence p')->where('p.seance_id = ?', $seance_id)->andWhere('p.parlementaire_id = ?', $parlementaire->id)->execute();
  }

  public function executeSeance(sfWebRequest $request)
  {
    $seance_id = $request->getParameter('seance');
    $this->seance = doctrine::getTable('Seance')->find($seance_id);
    $this->forward404Unless($this->seance);
  }
}

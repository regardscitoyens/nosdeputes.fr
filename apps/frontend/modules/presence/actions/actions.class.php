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
    $this->forward404Unless($seance_id);
    $this->seance = doctrine::getTable('Seance')->createQuery('s')
      ->where('s.id = ?', $seance_id)
      ->leftJoin('s.Organisme')
      ->fetchOne();
    $this->forward404Unless($this->seance);
    $this->presences = doctrine::getTable('Presence')->createQuery('p')
      ->where('p.seance_id = ?', $seance_id)
      ->leftJoin('p.Parlementaire pa')
      ->leftJoin('p.Preuves pr')
      ->groupBy('pa.id')
      ->orderBy('pr.source, pa.nom_de_famille')
      ->execute();
  }
}

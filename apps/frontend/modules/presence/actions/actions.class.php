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
    $query = Doctrine::getTable('Presence')->createQuery('p');
    if ($slug) {
        $query->where('p.Parlementaire.slug = ?', $slug);
    }
    $query->leftJoin('p.Seance.Organisme o')
        ->leftJoin('p.Preuves pr')
        ->orderBy('p.Seance.type ASC, p.Seance.date ASC');
    $this->presences = $query->execute();
    $this->forward404Unless($this->presences);
  }

  public function executeSeance(sfWebRequest $request)
  {
    $seance_id = $request->getParameter('seance');
    $this->seance = doctrine::getTable('Seance')->find($seance_id);
    $this->forward404Unless($this->seance);
  }
}

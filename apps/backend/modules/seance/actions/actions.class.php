<?php

require_once dirname(__FILE__).'/../lib/seanceGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/seanceGeneratorHelper.class.php';

/**
 * seance actions.
 *
 * @package    cpc
 * @subpackage seance
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class seanceActions extends autoSeanceActions
{

  public function executeSuppr(sfWebRequest $request) {
    $id = $request->getParameter('id');
    $this->forward404Unless($id);
    $seance = Doctrine::getTable('Seance')->find($id);
    $this->forward404Unless($seance);
    $this->orga = Doctrine::getTable('Organisme')->find($seance->organisme_id);
    $this->seances = Doctrine_Query::create()
      ->select('id, date, moment, session, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
      ->from('Seance s')
      ->where('s.id = ?', $id)
      ->leftJoin('s.Interventions i')
      ->leftJoin('s.Presences p')
      ->leftJoin('p.Preuves pr')
      ->groupBy('s.id')
      ->fetchArray();
    $this->forward404Unless(count($this->seances));
    if ($this->seances[0]['n_interventions'] != 0) {
      if ($this->orga)
        $this->redirect('@commission_suppr_seance?id='.$this->orga->id.'&seance=-1&pre=0&prp=0');
      $this->redirect('@list_commissions_suppr_seance?id=-1&pre=0&prp=0');
    }
    $this->presences = array();
    foreach (Doctrine_Query::create()->select('id')->from('PreuvePresence p')
      ->leftJoin('p.Presence pr')->where('pr.seance_id = ?', $id)->fetchArray() as $presence)
      $this->presences[] = $presence['id'];
    if ($request->getParameter('ok')) {
      if (count($this->presences)) {
        $query = Doctrine_Query::create()
          ->delete('PreuvePresence p')
          ->whereIn('p.id', $this->presences);
        $prp = $query->execute();
      } else $prp = 0;
      $query = Doctrine_Query::create()
        ->delete('Presence p')
        ->where('p.seance_id = ?', $id);
      $pre = $query->execute();
      $query = Doctrine_Query::create()
        ->delete('Seance s')
        ->where('s.id = ?', $id);
      if (! $query->execute())
        $id = 0;
      if ($this->orga)
        $this->redirect('@commission_suppr_seance?id='.$this->orga->id.'&seance='.$id.'&pre='.$pre.'&prp='.$prp);
      else $this->redirect('@list_commissions_suppr_seance?id='.$id.'&pre='.$pre.'&prp='.$prp);
    }
  }
}

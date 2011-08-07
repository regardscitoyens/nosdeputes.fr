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
    $this->ids = $request->getParameter('id');
    $this->forward404Unless($this->ids);
    
    $ids_arr = explode(',', $this->ids);
    $n_suppr = count($ids_arr);
    $this->seances = Doctrine_Query::create()
      ->select('id, date, moment, session, organisme_id, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
      ->from('Seance s')
      ->whereIn('s.id', $ids_arr)
      ->leftJoin('s.Interventions i')
      ->leftJoin('s.Presences p')
      ->leftJoin('p.Preuves pr')
      ->groupBy('s.id')
      ->orderBy('s.date, s.moment')
      ->fetchArray();
    $this->forward404Unless(count($this->seances) == $n_suppr);
    $this->orga = Doctrine::getTable('Organisme')->find($this->seances[0]['organisme_id']);
    if ($this->seances[0]['n_interventions'] != 0) {
      if ($this->orga)
        $this->redirect('@commission_suppr_seance?id='.$this->orga->id.'&seance=-1&pre=0&prp=0');
      $this->redirect('@list_commissions_suppr_seance?id=-1&pre=0&prp=0');
    }
    $this->presences = array();
    if (!$request->getParameter('ok')) {
      $query = Doctrine_Query::create()
        ->select('id')
        ->from('PreuvePresence p')
        ->leftJoin('p.Presence pr')
        ->whereIn('pr.seance_id', $ids_arr);
      foreach ($query->fetchArray() as $presence)
        $this->presences[] = $presence['id'];
    } else if ($request->getParameter('ok')) {
      $pre = 0;
      $prp = 0;
      foreach ($ids_arr as $id) {
        $this->presences = array();
        $query = Doctrine_Query::create()
          ->select('id')
          ->from('PreuvePresence p')
          ->leftJoin('p.Presence pr')
          ->where('pr.seance_id = ?', $id);
        foreach ($query->fetchArray() as $presence)
          $this->presences[] = $presence['id'];
        if (count($this->presences)) {
          $query = Doctrine_Query::create()
            ->delete('PreuvePresence p')
            ->whereIn('p.id', $this->presences);
          $prp += $query->execute();
        };
        $query = Doctrine_Query::create()
          ->delete('Presence p')
          ->where('p.seance_id = ?', $id);
        $pre += $query->execute();
        $query = Doctrine_Query::create()
          ->delete('Seance s')
          ->where('s.id = ?', $id);
        if (! $query->execute()) {
          $this->ids = 0;
          break;
        }
      }
      if ($this->orga)
        $this->redirect('@commission_suppr_seance?id='.$this->orga->id.'&seance='.$this->ids.'&pre='.$pre.'&prp='.$prp);
      else $this->redirect('@list_commissions_suppr_seance?id='.$this->ids.'&pre='.$pre.'&prp='.$prp);
    }
  }

  public function executeFuse(sfWebRequest $request) {

    $formaction = $request->getParameter('formaction');
    $this->forward404Unless($formaction && preg_match('/^(Fusionner les seances|Supprimer les seances)$/', $formaction));
    $orga = $request->getParameter('id');
    $this->forward404Unless($orga);
    
    if ($formaction == 'Fusionner les seances') {
      if (preg_match('/^(\d+),(\d+)$/', $orga, $match))
        $res_url = '@fuse?type=commission&bad='.$match[1].'&good='.$match[2].'&result=wrongdate';
      else $res_url = '@commission_fuse_seances?id='.$orga.'&result=wrongdate';
      $n_dates = $request->getParameter('dates');
      $this->forward404Unless($n_dates);
      $bad = "";
      $good = "";
      for ($i=1; $i <= $n_dates; $i++) if (($bd = $request->getParameter('bad'.$i)) && ($gd = $request->getParameter('good'.$i)))
        if ($bad == "") {
          $bad .= $bd;
          $good .= $gd;
        } else {
          $bad .= ','.$bd;
          $good .= ','.$gd;
        }
      if ($bad != "" && $good != "" && ($bad != $good))
        $this->redirect('@fuse?type=seance&id='.$orga.'&bad='.$bad.'&good='.$good);
      $this->redirect('@list_commissions?result=wrongform');
    } else {
      $n_seances = $request->getParameter('seances');
      $this->forward404Unless($n_seances);
      $suppr = "";
      for ($i=1; $i <= $n_seances; $i++) if ($sea = $request->getParameter('suppr'.$i)) {
        if ($suppr == "") $suppr .= $sea;
        else $suppr .= ','.$sea;
      }
      if ($suppr != "") $this->redirect('@seance_suppr?id='.$suppr);
      $this->redirect('@list_commissions?result=wrongform');
    }
  }
}

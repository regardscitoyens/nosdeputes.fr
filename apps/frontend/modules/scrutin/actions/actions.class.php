<?php
/**
 * scrutins components.
 *
 * @package    cpc
 * @subpackage scrutin
 */
class scrutinActions extends sfActions
{
  public function executeParlementaire(sfWebRequest $request) {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    myTools::setPageTitle("Votes de ".$this->parlementaire->nom, $this->response);

    $query = Doctrine::getTable('Scrutin')->createQuery('s')
      ->orderBy('s.date DESC');

    $scrutins = $query->execute();

    // log parsing errors
    foreach ($scrutins as $s) {
        if ($s->isOnWholeText() === null) {
            $this->logMessage("isOnWholeText can't parse ".$s->titre, "err");
        }
        if ($s->getLaw() === null) {
            $this->logMessage("getLaw can't parse ".$s->titre, "debug");
        }
    }

    // group scrutins by law and filter them
    $this->grouped_scrutins = array();
    $this->scrutins_ensemble = array();
    $current_group = false;
    foreach($scrutins as $s) {
        if (!$s->isOnWholeText()) {
            if ($current_group && $current_group[0]->getLaw() != $s->getLaw()) {
                $this->grouped_scrutins[] = $current_group;
                $current_group = array();
            }
            $current_group[] = $s;
        } else {
            $this->scrutins_ensemble[] = $s;
        }
    }
    if ($current_group) {
        $this->grouped_scrutins[] = $current_group;
    }

    $query = Doctrine::getTable('ParlementaireScrutin')->createQuery('ps')
      ->where('ps.parlementaire_id = ?', $this->parlementaire->id);

    $votes = $query->execute();
    $this->votes = array();
    foreach ($votes as $vote) {
        $this->votes[$vote->scrutin_id] = $vote;
    }
  }

  public function executeOne(sfWebRequest $request) {
    $this->scrutin = Doctrine::getTable('Scrutin')->findOneBy('numero',$request->getParameter('numero'));
    $this->forward404Unless($this->scrutin);
    myTools::setPageTitle($this->scrutin->titre, $this->response);

    $query = Doctrine::getTable('ParlementaireScrutin')->createQuery('ps')
      ->leftJoin('ps.Parlementaire p')
      ->orderBy('ps.parlementaire_groupe_acronyme, ps.position, p.nom')
      ->where('ps.scrutin_id = '.$this->scrutin->id);

    $votes = $query->execute();
    // group scrutins by law and filter them
    $this->grouped_votes = array();
    $current_group = false;
    foreach($votes as $v) {
        if ($current_group && $current_group[0]->parlementaire_groupe_acronyme != $v->parlementaire_groupe_acronyme) {
            $this->grouped_votes[] = $current_group;
            $current_group = array();
        }
        $current_group[] = $v;
    }
    if ($current_group) {
        $this->grouped_votes[] = $current_group;
    }

  }
}
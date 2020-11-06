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

    $this->scrutins = $query->execute();

    foreach ($this->scrutins as $s) {
        if ($s->isOnWholeText() === null) {
            $this->logMessage("isOnWholeText can't parse ".$s->titre, "err");
        }
        if ($s->getLaw() === null) {
            $this->logMessage("getLaw can't parse ".$s->titre, "debug");
        }
    }

    // group scrutins by law
    $this->grouped_scrutins = array();
    $current_group = false;
    foreach($this->scrutins as $s) {
        if (!$s->isOnWholeText()) {
            if ($current_group && $current_group[0]->getLaw() != $s->getLaw()) {
                $this->grouped_scrutins[] = $current_group;
                $current_group = array();
            }
            $current_group[] = $s;
        }
    }
    if ($current_group) {
        $this->grouped_scrutins[] = $current_group;
    }

    $query = Doctrine::getTable('ParlementaireScrutin')->createQuery('ps')
      ->where('ps.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('ps.Scrutin s')
      ->orderBy('s.date DESC');

    $votes = $query->execute();
    $this->votes = array();
    foreach ($votes as $vote) {
        $this->votes[$vote->scrutin_id] = $vote;
    }
  }
}
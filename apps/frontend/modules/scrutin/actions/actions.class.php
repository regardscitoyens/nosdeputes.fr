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
    }

    foreach ($this->scrutins as $s) {
        if ($s->getLaw() === null) {
            $this->logMessage("getLaw can't parse ".$s->titre, "debug");
        }
    }

    $query = Doctrine::getTable('ParlementaireScrutin')->createQuery('ps')
      ->where('ps.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('ps.Scrutin s')
      ->orderBy('s.date DESC');

    $this->votes = $query->execute();
  }
}
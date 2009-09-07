<?php
class presenceComponents extends sfComponents
{
  public function executeSeance()
  {
    $this->presences = doctrine::getTable('Presence')->createQuery('p')
      ->where('p.seance_id = ?', $this->seance)
      ->leftJoin('p.Parlementaire pa')
      ->groupBy('pa.id')
      ->orderBy('pa.nom_de_famille')
      ->execute();
  }
}

<?php
/**
 * scrutins actions.
 *
 * @package    cpc
 * @subpackage scrutin
 */
class scrutinComponents extends sfComponents
{
  public function executeParlementaire() {
    $query = Doctrine::getTable('ParlementaireScrutin')->createQuery('ps')
      ->where('ps.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('ps.position != ?', 'nonVotant')
      ->leftJoin('ps.Scrutin s')
      ->orderBy('s.date DESC');
    if (isset($this->limit))
      $query->limit($this->limit);
    $this->votes = $query->execute();
  }
}

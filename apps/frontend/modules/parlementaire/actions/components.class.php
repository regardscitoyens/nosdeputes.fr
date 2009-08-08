<?php 
class parlementaireComponents extends sfComponents
{
  public function executeList() 
  {
    $this->parlementaires = $this->parlementairequery
      ->select('p.*, count(i.id) as nb')
      ->groupBy('p.id')
      ->orderBy('nb DESC')
      ->execute();

  }
}
<?php 
class parlementaireComponents extends sfComponents
{
  public function executeList() 
  {
    $this->parlementaires = $this->parlementairequery
      ->select('p.*, count(pi.id) as nb')
      ->groupBy('p.id')
      ->orderBy('nb DESC')
      ->execute();
    
  }
}
<?php 
class parlementaireComponents extends sfComponents
{
  public function executeList() 
  {
    $this->parlementaires = $this->parlementairequery
      ->select('p.*, count(i.nb_mots) as nb')
      ->groupBy('p.id')
      ->orderBy('nb DESC')
      ->execute();

  }
}
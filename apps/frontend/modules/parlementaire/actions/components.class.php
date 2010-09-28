<?php 
class parlementaireComponents extends sfComponents
{
  public function executeList() 
  {
    $this->parlementaires = $this->parlementairequery
      ->select('p.*, i.id, count(i.id) as nb')
      ->groupBy('p.id')
      ->orderBy('nb DESC')
      ->execute();

  }
  public function executeHeader()
  {
  }
  public function executeDuJour()
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->createQuery('p')->where('fin_mandat IS NULL')->orderBy('rand()')->fetchOne();
    return ;
  }
}
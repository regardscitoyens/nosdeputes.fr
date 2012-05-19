<?php
class parlementaireComponents extends sfComponents
{
  public function executeList()
  {
    $this->parlementaires = array();
    if (!isset($this->interventions) || !count($this->interventions)) 
      return ;
    $this->parlementaires = Doctrine::getTable('Intervention')->createQuery('i')
      ->leftJoin('i.Parlementaire p')
      ->whereIn('i.id', $this->interventions)
      ->andWhere('((i.fonction != ? AND i.fonction != ? ) OR i.fonction IS NULL)', array('président', 'présidente'))
      ->andWhere('i.parlementaire_id IS NOT NULL')
      ->select('p.nom, p.slug, i.id, count(i.id) as nb')
      ->groupBy('p.id')
      ->orderBy('nb DESC')
      ->fetchArray();

  }
  public function executeHeader()
  {
  }
  public function executeDuJour()
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->createQuery('p')->where('fin_mandat IS NULL')->orderBy('rand()')->limit(1)->fetchOne();
    return ;
  }
  public function executeSearch() {
    $this->search = $this->query;

    $query = Doctrine::getTable('Parlementaire')->createQuery('p');

    $searchs = explode(' ', preg_replace('/\W/', ' ', $this->search));
    $ns = count($searchs);
    for ($i=0; $i<$ns; $i++)
      $searchs[$i] = '%'.$searchs[$i].'%';
    $likes = 'p.nom LIKE ?';
    for ($i=1; $i<$ns; $i++)
      $likes .= ' AND p.nom LIKE ?';
    $query->where($likes, $searchs);
    $query->orderBy('p.nom_de_famille ASC');

    $this->parlementaires = $query->execute();

    $nb = count($this->parlementaires);
    if ($nb == 0) {
      $this->similars = Doctrine::getTable('Parlementaire')->similarTo($this->search, null, 1);
    }
  }

  public function executeWidget() {
    $search = $this->slug;
    $this->parl = Doctrine::getTable('Parlementaire')->findOneBySlug($this->slug);
    if (!$this->options)
      $this->options = array('titre' => 1, 'photo' => 1, 'graphe' => 1, 'activite' => 1, 'tags' => 1, 'iframe' => 0, 'width' => 935, 'maxtags' => 40);
  }  
}

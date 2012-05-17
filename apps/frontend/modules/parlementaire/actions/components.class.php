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
    $search = $this->depute;
    $sexe = null;
    if (preg_match("/M\([.mle]\)+ */", $search, $match)) {
      $sexe = "H";
      if (preg_match("/e/", $match[1])) 
        $sexe = "F";
      $search = preg_replace("/^.*M\([.mle]\)+ */", "", $search);
    }
    $search = preg_replace("/([ \-.]\w)/", strtoupper("\\1"), ucfirst(strtolower($search)));
    $this->parl = Doctrine::getTable('Parlementaire')->findOneBySlug(strtolower($search));
    if (!$this->parl)
      $this->parl = Doctrine::getTable('Parlementaire')->findOneByNom($search);
    if (!$this->parl)
      $this->parl = Doctrine::getTable('Parlementaire')->findOneByNomDeFamille($search);
#    if (!$this->parl)
#      $this->parl = Doctrine::getTable('Parlementaire')->findOneByNomSexeGroupeCirco($search, $sexe);
    if (!$this->options)
      $this->options = array('titre' => 1, 'photo' => 1, 'graphe' => 1, 'activite' => 1);
  }  
}

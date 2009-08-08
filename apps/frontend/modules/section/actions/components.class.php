<?php

class sectionComponents extends sfComponents
{
  public function executeParlementaire()
  {
    $this->textes = doctrine_query::create()
      ->from('Section s')
      ->select('s.section_id, sp.titre, count(i.id) as nb')
      ->where('s.section_id = sp.id')
      ->leftJoin('s.Section sp')
      ->leftJoin('s.Interventions i')
      ->andWhere('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.nb_mots > 20')
      ->groupBy('s.section_id')
      ->orderBy('nb DESC')
      ->fetchArray();
  }
}
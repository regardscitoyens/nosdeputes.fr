<?php

class sectionComponents extends sfComponents
{
  public function executeParlementaire()
  {
    $sql = doctrine_query::create()
      ->from('Section s')
      ->select('s.section_id, sp.titre, count(i.id) as nb')
      ->where('s.section_id = sp.id')
      ->leftJoin('s.Section sp')
      ->leftJoin('s.Interventions i')
      ->andWhere('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.nb_mots > 20')
      ->groupBy('s.section_id');

    if (isset($this->order) && $this->order == 'date') {
      $sql->orderBy('i.date DESC')->groupBy('s.section_id, i.date');
    } else {
      $sql->orderBy('nb DESC');
    }
    $this->textes = $sql->fetchArray();
    if (isset($this->order) && $this->order == 'date') {
      $done = array();
      for ($i=0; $i<count($this->textes); $i++) {
        if (isset($done[$this->textes[$i]['section_id']])) {
          $this->textes[$done[$this->textes[$i]['section_id']]]['nb'] += $this->textes[$i]['nb'];
          unset($this->textes[$i]);
        } else $done[$this->textes[$i]['section_id']] = $i;
      }
    }
  }
}

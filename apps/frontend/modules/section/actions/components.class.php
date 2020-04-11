<?php

class sectionComponents extends sfComponents
{
  public function executeSimplifions() {
    $this->lois = Doctrine_Query::create()->select('l.texteloi_id, l.titre, l.nb_commentaires')->from('TitreLoi l')->where('l.chapitre IS NULL')->andWhere('l.section IS NULL')->andWhere('l.nb_commentaires >= 5')->orderBy('l.date DESC')->fetchArray();
  }

  public function executeParlementaire() {
    $sql = Doctrine_Query::create()
      ->select('s.section_id, sp.titre, count(i.id) as nb')
      ->from('Section s')
      ->where('s.section_id = sp.id')
      ->leftJoin('s.Section sp')
      ->leftJoin('s.Interventions i')
      ->andWhere('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.nb_mots > 20')
      ->groupBy('s.section_id');

    if (isset($this->order) && $this->order == 'date') {
      $sql->orderBy('i.date DESC')->groupBy('s.section_id, i.date');
      if (isset($this->limit))
	$sql->limit($this->limit*5);
    } else $sql->orderBy('nb DESC');
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

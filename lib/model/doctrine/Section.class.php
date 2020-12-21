<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Section extends BaseSection
{
  public function getLink() {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    return url_for('@section?id='.$this->id);
  }
  public function getPersonne() {
    return '';
  }
  public function getLinkSource() {
    if ($this->id_dossier_an) {
      return "https://www.assemblee-nationale.fr/".sfConfig::get('app_legislature', 13)."/dossiers/".$this->id_dossier_an.".asp";
    }
    return "";
  }
  public function __tostring() {
    if ($str = $this->_get('titre_complet'))
      return $str;
    return "";
  }

  public function getParlementaires() {
    if (!$this->getIsParent())
      return "";
    $parls = Doctrine_query::create()->select('p.*')->from('Parlementaire p')
      ->leftJoin('p.Interventions i')
      ->leftJoin('i.Section s')
      ->where('s.id = ? OR s.section_id = ?', array($this->id, $this->id))
      ->andWhere('i.nb_mots > 20')
      ->groupBy('p.id')
      ->execute();
    return $parls;
  }

  public function setTitreComplet($titre) {
    $this->_set('titre_complet', $titre);
    $this->md5 = md5($titre);
    $titres = preg_split('/\s*>\s*/', $titre);
    $parent = null;
    if (count($titres) > 1) {
      $parent_titre = array_shift($titres);
      $parent = Doctrine::getTable('Section')->findOneByContexteOrCreateIt($parent_titre);
    }
    $this->_set('titre', $titres[0]);
    $this->save();
    if (!$parent)
      $parent = $this;
    $this->section_id = $parent->id;
    $this->save();
  }
  public function getSubSections() {
    return $q = Doctrine::getTable('Section')->createQuery('s')
      ->where('s.section_id = ?', $this->id)
      ->orderBy('s.min_date ASC, s.timestamp ASC')->execute();
  }
  public function getFirstSeance() {
    return Doctrine_Query::create()
      ->from('Seance s, Section st, Intervention i')
      ->select('s.id')
      ->where('i.seance_id = s.id')
      ->andwhere('i.section_id = st.id')
      ->andWhere('(st.section_id = ? OR i.section_id = ? )', array($this->id, $this->id))
      ->groupBy('s.id')
      ->orderBy('st.min_date ASC, st.timestamp ASC')
      ->limit(1)
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }
  public function getSeances() {
    $q = Doctrine_Query::create()
      ->from('Seance s, Section st, Intervention i')
      ->select('s.*')
      ->where('i.seance_id = s.id')
      ->andwhere('i.section_id = st.id')
      ->andwhere('(st.section_id = ? OR i.section_id = ? )', array($this->id, $this->id))
      ->groupBy('s.id')
      ->orderBy('s.date, s.moment');
    return $q->execute();
  }

  public function updateNbInterventions() {
    $a = Doctrine_Query::create()
      ->select('count(*) as nb')
      ->from('Intervention i')
      ->leftJoin('i.Section s')
      ->where('(i.section_id = ? OR s.section_id = ?)', array($this->id, $this->id))
      ->andWhere('(i.fonction NOT LIKE ? AND i.fonction NOT LIKE ?)', array('président', 'présidente'))
      ->andWhere('i.nb_mots > 20')
      ->fetchArray();
    $this->_set('nb_interventions', $a[0]['nb']);
    $this->save();
  }

  public function getSection($check = 0) {
    if ($this->id == $this->section_id)
      if ($check == 0) return NULL;
      else return $this;
    return $this->_get('Section');
  }

  public function getIsParent() {
    if ($this->section_id == $this->id && !preg_match('/questions/i', $this->titre))
      return true;
    return false;
  }

  public function getTitre() {
    return myTools::betterUCFirst(preg_replace('/\s*\?$/', '', $this->_get('titre')));
  }

  public function getOrigTitre() {
    return $this->_get('titre');
  }

  public function setMaxDate($date) {
    if ($this->max_date && $date <= $this->max_date)
      return;
    $this->_set('max_date', $date);
    $this->save();
  }

}
?>

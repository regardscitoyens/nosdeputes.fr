<?php

require_once "myTools.class.php";

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Organisme extends BaseOrganisme
{
  public function getLink() {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    return url_for('@list_parlementaires_organisme?slug='.$this->slug);
  }
  public function getTitre() {
    if ($this->type === "groupe")
      return $this->getNom().' ('.$this->getSmallNomGroupe().')';
    else return $this->getNom();
  }
  public function getPersonne() {
    return '';
  }
  public function __toString() {
    if ($titre = $this->getTitre())
      return substr($titre, 0, 100);
    return "";
  }
  public function getSmallNomGroupe() {
    $hashmap = array();
    foreach (myTools::getGroupesInfos() as $gpe)
      $hashmap[strtolower($gpe[0])] = $gpe[1];
    if (isset($hashmap[strtolower($this->getNom())]))
      return $hashmap[strtolower($this->getNom())];
    return "";
  }
  public static function getNomByAcro($acro) {
    $acro = strtolower($acro);
    $hashmap = array();
    foreach (myTools::getGroupesInfos() as $gpe)
      $hashmap[strtolower($gpe[1])] = $gpe[0];
    if (preg_match('/^('.implode('|',array_keys($hashmap)).')$/i', $acro))
      return $hashmap["$acro"];
    else return false;
  }
  public function getSeanceByDateAndMomentOrCreateIt($date, $moment, $session = null) {
    $seance = $this->getSeanceByDateAndMoment($date, $moment);
    if (!$seance) {
      $seance = new Seance();
      $seance->type = 'commission';
      $seance->setDate($date);
      $seance->moment = Seance::convertMoment($moment);
      $seance->Organisme = $this;
      $seance->setSession($session);
      $seance->save();
    }
    return $seance;
  }

  public function getNom() {
    if ($this->_get('nom') == 'assemblée nationale')
      return "Bureau de l'Assemblée Nationale";
    else return ucfirst($this->_get('nom'));
  }

  public function getSeanceByDateAndMoment($date, $moment) {
    $moment = Seance::convertMoment($moment);
    $q = Doctrine::getTable('Seance')->createQuery('s');
    $q->where("organisme_id = ?", $this->id)->andWhere('date = ?', $date)->andWhere('moment = ?', $moment);
    $res = $q->fetchOne();
    $q->free();
    unset($q);
    if ($res) {
      return $res;
    }
    $q = Doctrine::getTable('Seance')->createQuery('s');
    $q->where("organisme_id = ?", $this->id)->andWhere('date = ?', $date);
    if (count($q->fetchArray()) && preg_match('/(\d+)[:](\d+)/', $moment, $match)) {
      $q = Doctrine::getTable('Seance')->createQuery('s');
      $q->where("organisme_id = ?", $this->id)->andWhere('date = ?', $date)->andWhere('moment LIKE ?', $match[1].':%');
      $res = $q->fetchOne();
      $q->free();
      unset($q);
    }
    return $res;
  }

  public function getIsToIndex() {
    if ($this->type === "extra")
      return false;
    return true;
  }

  public function getHasParlementaires() {
    if (count($this->getParlementaires()))
      return true;
    return false;
  }
}

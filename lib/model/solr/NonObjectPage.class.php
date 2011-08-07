<?php

class NonObjectPage
{
  private $elements;
  
  public static function getElements() {
    return sfYaml::load(sfConfig::get('sf_config_dir').'/solr/url.yml');
  }

  public static function find($id) {
    $array = self::getElements();
    $obj = new NonObjectPage();
    $obj->elements = $array[$id];
    return $obj;
  }

  public function __toString() {
    return $this->getTitre();
  }

  private function getElement($e) {
    if (!isset($this->elements[$e])) {
      return '';
    }
    return $this->elements[$e];
  }

  public function getTitre() {    
    return $this->getElement('title');
  }

  public function getLink() {
    return $this->getElement('url');
  }

  public function getDescription() {
    return $this->getElement('description');
  }

  public function getPersonne() {
    return $this->getElement('personne');
  }

  public function getImage() {
    return $this->getElement('image');
  }
}
<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Citoyen extends BaseCitoyen
{
  public function getLink() {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    return url_for('@citoyen?slug='.$this->slug);
  }
  public function getTitre() {
    return $this->getLogin();
  }
  public function getPersonne() {
    return '';
  }
  public function __toString() {
    return $this->getLogin();
  }

  public function isPasswordCorrect($password) {
    return ($this->password == sha1($password));
  }
  public function setPassword($password) {
    return $this->_set('password', sha1($password));
  }
  public function setOneParametre($key, $value) {
    $params = $this->getParametres();
    $params[$key] = $value;
    return $this->setParametres($params);
  }

  public function getOneParametre($key) {
    $params = $this->getParametres();
    return $params[$key];
  }

  public function getParametres() {
    $p = $this->_get('parametres');
    $a = VariableGlobale::json_decode_or_unserialize($p);
    if (!is_array($a)) {
      $a = array();
    }
    return $a;
  }
  public function setParametres($array) {
    if (!empty($array) && !is_array($array)) {
      throw new Exception('Parametres requires an array');
    }
    if(is_array($array)) { return $this->_set('parametres', json_encode($array)); }
  }
}

<?php


class TexteloiTable extends ObjectCommentableTable {  

  public static function getInstance() {
    return Doctrine_Core::getTable('Texteloi');
  }

  public function findByNumAnnexe($num, $annexe) {
    $doc = $this->createQuery('t')
      ->where('numero = ?', $num)
      ->andWhere('annexe = ?', $annexe)
      ->fetchOne();
    if (!$doc) return null;
    return $doc;
  }

  public function findLoi($num) {
    $doc = $this->createQuery('t')
      ->where('id = ? or id = ? or id like ?', array($num, $num."-a0", $num."-t%"))
      ->andWhere('type = ? or type = ? or type = ? or type = ?', array('Projet de loi', 'Proposition de loi', 'Proposition de résolution', 'Texte de la commission'))
      ->orderBy('numero, annexe')
      ->fetchOne();
    if (!$doc) return null;
    return $doc;
  }


}

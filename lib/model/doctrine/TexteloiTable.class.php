<?php


class TexteLoiTable extends ObjectCommentableTable {  

  public static function getInstance() {
    return Doctrine_Core::getTable('TexteLoi');
  }

  public function findLoi($num) {
    $doc = $this->createQuery('t')
      ->where('id in ? or id like ?', array(array($num, $num."-a0"), $num."-t%"))
      ->andWhere('type in ?', array('Projet de loi', 'Proposition de loi', 'Proposition de résolution', 'Texte de la commission'))
      ->orderBy('numero, annexe')
      ->fetchOne();
    if (!$doc) return null;
    return $doc;
  }


}

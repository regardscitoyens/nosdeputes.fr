<?php

class makeLiasseTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'make';
    $this->name = 'Liasse';
    $this->briefDescription = 'Génère PDF imprimable des amendements dans l\'ordre du texte pour un projet de loi';
    $this->addArgument('numero_loi', sfCommandArgument::REQUIRED, 'Numéro de loi');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
 }

  protected function execute($arguments = array(), $options = array()) {
    $file = dirname(__FILE__).'/../../batch/amendements/liasse_order.tmp';
    $manager = new sfDatabaseManager($this->configuration);
    $amdmts = Doctrine::getTable('Amendement')->findByTexteloiId($arguments['numero_loi']);
    if ($amdmts) {
      $amdmts_idx = array();
      foreach ($amdmts as $amdmt) $amdmts_idx[$amdmt->getNumero()] = $amdmt;
      echo "********************************************************************************\n";
      echo "***************************   Projet de Loi N°".sprintf("%4s",$arguments['numero_loi'])."   ***************************\n";
      echo "****************************   ".sprintf("%4s",count($amdmts))." amendements    *****************************\n";
      foreach (file($file) as $line) {
	if (preg_match('/(\d+)/', $line, $match))
          $num = $match[1];
        else continue;
	$amdmt = $amdmts_idx[$num];
        echo "********************************************************************************\n";
        if (!$amdmt) {
	  echo "Amendement N°".$num." : informations manquantes\n";
	  continue;
	} else {
	  echo "   ".$amdmt->getTitreNoLink()." -- ".$amdmt->getSujet()." -- de ".$amdmt->getSignataires()."\n";
          echo "     ".strip_tags($amdmt->getTexte(0))."\n";
          echo "     EXPOSE : ".strip_tags($amdmt->getExpose())."\n";
        }
      }
    }
    unlink($file);
  }
}

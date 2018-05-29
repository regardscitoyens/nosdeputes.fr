<?php

class printDumpAmendementsLoiTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'print';
    $this->name = 'dumpAmendementsLoi';
    $this->briefDescription = 'dump un csv contenant tous les amendements sur un texte de loi';
    $this->addArgument('loi_id', sfCommandArgument::REQUIRED, 'Numero de loi');
    $this->addArgument('format', sfCommandArgument::REQUIRED, 'Numero de loi');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'dev');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);
    $context = sfContext::createInstance($this->configuration);
    $this->configuration->loadHelpers(array('Url'));
    $loi = $arguments['loi_id'];
    $amendements = Doctrine::getTable('Amendement')->createQuery('a')
      ->select('a.id, a.legislature, a.texteloi_id, a.numero, CAST( a.numero AS SIGNED ) AS num, a.sous_amendement_de, a.rectif, a.sujet, a.sort, a.date, a.texte, a.expose, a.content_md5 as cle_unicite, a.signataires, a.source, a.nb_multiples, a.auteur_groupe_acronyme')
      ->from('Amendement a')
      ->where('a.sort <> ?', 'Rectifié')
      ->andWhere('a.texteloi_id = ?', $loi)
      ->orderBy('num')
      ->fetchArray();
    $champs = array();
    $res = array('amendements' => array());
    foreach ($amendements as $a) {
      if (!$a['auteur_groupe_acronyme'])
        $a['auteur_groupe_acronyme'] = Doctrine_Query::create()
          ->select('pa.parlementaire_groupe_acronyme')
          ->from('ParlementaireAmendement pa')
          ->where('pa.amendement_id = ?', $a['id'])
          ->andWhere('pa.numero_signataire = 1')
          ->limit(1)
          ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
      if (!$a['auteur_groupe_acronyme'])
        $a['auteur_groupe_acronyme'] = "";
      $parlslugs = Doctrine_Query::create()->select('p.slug')->from('Parlementaire p')->leftJoin('p.ParlementaireAmendements pa')->where('pa.amendement_id = ?', $a['id'])->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
      if (is_string($parlslugs)) $parlslugs = array($parlslugs);
      $a['parlementaires'] = myTools::array2hash($parlslugs, 'parlementaire');
      $a['url_nosdeputes'] = preg_replace('#http://(symfony/)+#', sfConfig::get('app_base_url'), url_for('@amendement?loi='.$loi.'&numero='.$a['numero'], 'absolute=true'));
      unset($a['num']);
      foreach(array_keys($a) as $key)
        if (!isset($champs[$key]))
          $champs[$key] = 1;
      $res['amendements'][] = array("amendement" => $a);
    }
    $breakline = 'amendement';
    switch($arguments['format']) {
      case 'csv':
        myTools::depile_csv($res, $breakline, array('parlementaire' => 1, 'groupe' => 1), $champs);
	break;
      case 'xml':
        myTools::depile_xml($res, $breakline);
	break;
      case 'json':
        echo json_encode($res);
	break;
      default:
        echo "Please input format csv, json or xml.";
    }
  }
}


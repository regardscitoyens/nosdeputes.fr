<?php

class printDumpAmendementsLoiCsvTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'print';
    $this->name = 'dumpAmendementsLoiCsv';
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
      ->select('a.id, a.legislature, a.texteloi_id, a.numero, CAST( a.numero AS SIGNED ) AS num, a.sous_amendement_de, a.rectif, a.sujet, a.sort, a.date, a.texte, a.expose, a.content_md5 as cle_unicite, a.signataires, a.source, a.nb_multiples')
      ->from('Amendement a')
      ->where('a.sort <> ?', 'Rectifié')
      ->andWhere('a.texteloi_id = ?', $loi)
      ->orderBy('num')
      ->fetchArray();
    $champs = array();
    $res = array('amendements' => array());
    foreach ($amendements as $a) {
      $parlslugs = Doctrine_Query::create()->select('p.slug')->from('Parlementaire p')->leftJoin('p.ParlementaireAmendements pa')->where('pa.amendement_id = ?', $a['id'])->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
      if (is_string($parlslugs)) $parlslugs = array($parlslugs);
      $parlgroup = array();
      foreach (Doctrine_Query::create()->select('count(pa.id) as ct, parlementaire_groupe_acronyme, p.groupe_acronyme as curgroupe')->from('ParlementaireAmendement pa')->leftJoin('pa.Parlementaire p')->where('pa.amendement_id = ?', $a['id'])->groupBy('parlementaire_groupe_acronyme, p.groupe_acronyme')->orderBy('parlementaire_groupe_acronyme, p.groupe_acronyme')->fetchArray() as $s) {
        $gpe = $s[(!$s["parlementaire_groupe_acronyme"] ? 'curgroupe' : 'parlementaire_groupe_acronyme')];
        if (!isset($parlgroup[$gpe])) $parlgroup[$gpe] = 0;
        $parlgroup[$gpe] += $s["ct"];
      }
      $groupes = [];
      foreach(array_keys($parlgroup) as $k)
        $groupes[] = "$k:".$parlgroup[$k];
      $a['parlementaires'] = myTools::array2hash($parlslugs, 'parlementaire');
      $a['groupes_parlementaires'] = myTools::array2hash($groupes, 'groupe');
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
        foreach(array_keys($champs) as $key)
          echo "$key;";
        echo "\n";
        myTools::depile_csv($res, $breakline, array('parlementaire' => 1, 'groupe'=>1));
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


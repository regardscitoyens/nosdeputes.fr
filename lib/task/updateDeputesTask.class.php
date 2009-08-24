<?php

class updateDeputesTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'update';
    $this->name = 'Deputes';
    $this->briefDescription = 'Update Deputes';
  }
 
  protected function splitArrayJson($json) {
    $res = array();
    foreach($json as $j) {
      if ($j)
	array_push($res, explode(' / ', $j));
    }
    return $res;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $dir = dirname(__FILE__).'/../../batch/depute/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  $sections = array();
	  if (preg_match('/^\./', $file))
	    continue;
	  echo "$dir$file\n";
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
	    if (!$json->nom)
	      next;
	    $parl = doctrine::getTable('Parlementaire')->findOneByNom($json->nom);
	    if (!$parl) {
	      $parl = new Parlementaire();
	      $parl->type = 'depute';
	      $parl->nom = $json->nom;
	      $parl->nom_de_famille = $json->nom_de_famille;
	      $parl->sexe = $json->sexe;
	    }
	    if (count($json->adresses))
	      $parl->adresses = $json->adresses;
	    if (count($json->autresmandats))
	      $parl->autres_mandats = $json->autresmandats;
	    if (count($json->extras))
	      $parl->extras = $this->splitArrayJson($json->extras);
	    if (count($json->fonctions))
	      $parl->fonctions = $this->splitArrayJson($json->fonctions);
	    if ($json->groupe)
	      $parl->groupe = $this->splitArrayJson($json->groupe);
	    $parl->debut_mandat = $json->debut_mandat;
	    $parl->fin_mandat = $json->fin_mandat;
	    if ($json->id_an)
	      $parl->id_an = $json->id_an;
	    if (count($json->mails))
		$parl->mails = $json->mails;
	    if ($json->photo)
	      $parl->photo = $json->photo;
	    if ($json->place_hemicycle)
	      $parl->place_hemicycle = $json->place_hemicycle;
	    if ($json->profession)
	      $parl->profession = $json->profession;
	    if ($json->site_web)
	      $parl->site_web = $json->site_web;
	    if ($json->url_an)
	      $parl->url_an = $json->url_an;
	    $parl->save();
	  }
	}
      }
    }
  }
}

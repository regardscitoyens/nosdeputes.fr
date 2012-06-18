<?php

class updateDeputesTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'update';
    $this->name = 'Deputes';
    $this->briefDescription = 'Update Deputes';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
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

    $villes = json_decode(file_get_contents($dir.'../static/villes.json'));

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  $sections = array();
	  if (preg_match('/^\./', $file))
	    continue;
//	  echo "$dir$file\n";
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
	    if (!isset($json->nom) || !strlen($json->nom)) {
	      echo "WARNING: ".$dir.$file." doesn't appear to be a json file\n";
	      continue;
	    }
	    $json->nom = trim($json->nom);
	    //	    echo "-".$json->nom.strlen($json->nom)." ".$json->id_an."\n";
	    $parl = Doctrine::getTable('Parlementaire')->findOneByNom($json->nom);
	    if (!$parl) {
	      $parl = new Parlementaire();
	      $parl->type = 'depute';
	      $parl->nom = $json->nom;
	      $parl->nom_de_famille = $json->nom_de_famille;
	      $parl->sexe = $json->sexe;
	    }
            if ($json->date_naissance)
              $parl->date_naissance = $json->date_naissance;
            if ($json->lieu_naissance)
              $parl->lieu_naissance = $json->lieu_naissance;
	    if ($json->circonscription)
	      $parl->circonscription = $json->circonscription;
	    else {
	      if ($json->departement)
	        $parl->setDepartementParNumero($json->departement);
              if ($json->num_circonscription)
                $parl->num_circo = $json->num_circonscription;
	    }
	    if (count($json->adresses))
	      $parl->adresses = $json->adresses;
            if (count($json->premiers_mandats))
              $parl->anciens_mandats = $json->premiers_mandats;
	    if (count($json->autresmandats))
	      $parl->autres_mandats = $json->autresmandats;
            if (count($json->anciensmandats))
              $parl->anciens_autres_mandats = $json->anciensmandats;
	    if ($json->groupe)
	      $parl->groupe = $this->splitArrayJson($json->groupe);
            if (count($json->fonctions))
              $parl->fonctions = $this->splitArrayJson($json->fonctions);
	    if (count($json->extras))
	      $parl->extras = $this->splitArrayJson($json->extras);
	    if (count($json->groupes))
	      $parl->groupes = $this->splitArrayJson($json->groupes);
	    $parl->debut_mandat = $json->debut_mandat;
	    $parl->fin_mandat = $json->fin_mandat;
	    if ($json->id_an)
	      $parl->id_an = $json->id_institution;
	    if (count($json->mails))
		$parl->mails = $json->mails;
	    if ($json->photo)
	      $parl->photo = $json->photo;
	    if ($json->place_hemicycle)
	      $parl->place_hemicycle = $json->place_hemicycle;
	    if ($json->profession)
	      $parl->profession = $json->profession;
	    if (count($json->sites_web))
	      $parl->sites_web = $json->sites_web;
            else if ($parl->sites_web && !preg_match('/^a:/', $parl->sites_web))
              $parl->sites_web = array($parl->sites_web);
	    if ($json->url_an)
	      $parl->url_an = $json->url_institution;
            if ($json->suppleant_de)
              $parl->setSuppleantDe($json->suppleant_de);
            if ($json->url_ancien_cpc)
              $parl->url_ancien_cpc = $json->url_ancien_cpc;
            if ($json->url_nouveau_cpc)
              $parl->url_nouveau_cpc = $json->url_nouveau_cpc;
	    $parl->villes = $villes->{$parl->getNumDepartement()}->{$parl->num_circo};
	    $parl->save();
            if ($json->suppleant) {
              $suppl = Doctrine::getTable('Parlementaire')->findOneByNom(preg_replace('/^\s*M[.mle]* /', '', $json->suppleant));
              if ($suppl) {
                $suppl->setSuppleantDe($parl->nom);
                $suppl->save();
              }              
            }
	  }
	}
      }
    }
  }
}

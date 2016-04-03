<?php

class updateSenateursTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'update';
    $this->name = 'Senateurs';
    $this->briefDescription = 'Update Senateurs';
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
    $dir = dirname(__FILE__).'/../../batch/senateur/out/';
    $manager = new sfDatabaseManager($this->configuration);

    $villes = json_decode(file_get_contents($dir.'../static/villes.json'));

    $sites = array();
    $row = 0;
    if (($handle = fopen($dir."../twitter.csv", "r")) !== FALSE) {
        fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row++;
            if ($row == 1) next;
            $sites[$data[18]] = explode("|", $data[16]);
            $sites[$data[18]][] = "https://twitter.com/".$data[0];
        }
        fclose($handle);
    }

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
	    //	    echo "-".$json->nom.strlen($json->nom)." ".$json->id_institution."\n";
	    if (preg_match('/(\d{4})$/', $json->fin_mandat, $match)) {
              if ($match[1] < 2004) continue;
            }
	    $parl = Doctrine::getTable('Parlementaire')->findOneByNom($json->nom);
	    if (!$parl) {
		echo "WARNING: New senateurs : ".$json->nom." \n";
	      $parl = new Parlementaire();
	      $parl->type = 'senateur';
	      $parl->nom = $json->nom;
	      $parl->nom_de_famille = $json->nom_de_famille;
	      $parl->sexe = $json->sexe;
	    }
	    if ($json->naissance)
               $parl->date_naissance = $json->naissance;
	    if ($json->circonscription)
	      $parl->circonscription = $json->circonscription;
	    if (count($json->adresses))
	      $parl->adresses = $json->adresses;
	    if (count($json->autresmandats))
	      $parl->autres_mandats = $json->autresmandats;
	    if (count($json->premiers_mandats))
              $parl->anciens_mandats = $json->premiers_mandats;
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
	    if ($json->id_institution)
	      $parl->id_institution = $json->id_institution;
	    if (count($json->mails))
		$parl->mails = $json->mails;
	    if ($json->photo)
	      $parl->photo = $json->photo;
	    if ($json->place_hemicycle)
	      $parl->place_hemicycle = $json->place_hemicycle;
	    if ($json->profession)
	      $parl->profession = $json->profession;
	    if (count($json->sites_web))
          foreach (array_keys($json->sites_web) as $i)
            $json->sites_web[$i] = preg_replace("|(://[^/]+)/$|", "$1", $json->sites_web[$i]);
        if (isset($sites[$parl->slug]) && count($sites[$parl->slug])) {
          foreach ($sites[$parl->slug] as $site)
            $sites[$parl->slug][$i] = preg_replace("|(://[^/]+)/$|", "$1", $sites[$parl->slug][$i]);
          if (count($json->sites_web))
            $json->sites_web = array_unique(array_merge($json->sites_web, $sites[$parl->slug]));
          else $json->sites_web = array_unique($sites[$parl->slug]);
        }
	    if (count($json->sites_web))
	      $parl->sites_web = $json->sites_web;
	    if ($json->url_institution)
	      $parl->url_institution = $json->url_institution;
	    if ($json->suppleant_de)
	      $parl->setSuppleantDe($json->suppleant_de);
            $vi = "";
	    if ($villes->{$parl->getNumDepartement()}) foreach(get_object_vars($villes->{$parl->getNumDepartement()}) as $v) {
              if ($vi) $vi .= ", ";
              $vi .= $v;
            }
            $parl->villes = $vi;
	    $parl->save();
	  }
	}
      }
    }
  }
}

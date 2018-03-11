<?php

class tagSeanceTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'tag';
    $this->name = 'Seance';
    $this->briefDescription = 'Tag Seance';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function count($array, $excludeS = 0) {
    foreach($array as $i) {
      $i = strip_tags($i['intervention']);
      $i = preg_replace('/<[^>]+>/', '', $i);
      $i = html_entity_decode(str_replace('&nbsp;', ' ', htmlentities($i, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');
      $i = preg_replace('/\([^\)]+\)/', '', $i);
      $i = preg_replace('/&#339;/', 'oe', $i);
      $i = str_replace(array(',',';','.',':','_','(',')','&','#','<','>','\'','"','«','»',' -','- ','?','!'), ' ', $i);
      $i = preg_replace('/\s+/', ' ', $i);
      foreach(explode(" ", $i) as $w) {
	if (!preg_match('/^[A-Z]+$/', $w))
	  $w = strtolower($w);
	if (strlen($w)>2 && preg_match('/[a-z]/i', $w)) {
	  //	  $s = soundex($w);
	  $s = $w;
	  if (!isset($words[$s]))
	    $words[$s] = 1;
	  else
	    $words[$s]++;
	  if (!isset($this->sound[$s]))
	    $this->sound[$s] = $w;
	}
      }
    }
    foreach(array_keys($words) as $k) {
      if (preg_match('/s$/', $k)) {
	$ks = preg_replace('/s$/', '', $k);
	if (isset($words[$ks])) {
	  $words[$ks]+=$words[$k];
	  if ($excludeS)
	    unset($words[$k]);
	}
      }
    }
    arsort($words);
    return $words;
  }

  protected function execute($arguments = array(), $options = array())
  {

    // your code here
    $manager = new sfDatabaseManager($this->configuration);
    $q = Doctrine_Query::create();
    $q->select('intervention')->from('Intervention i')->where('i.parlementaire_id IS NOT NULL and i.nb_mots > 10')->orderBy('i.date DESC')->limit(100000);
    $array = $q->fetchArray();
    echo "count:\n\t";
    echo count($array)."\n";
    $words = $this->count($array);
    $cpt = 0;
    $tot = count($words);

    $exclude = array('vice-président' => 1, 'restant' => 1, 'mixte' => 1, 'paritaire' => 1, 'rapporteure' => 1, 'rapporteur' => 1, 'députée' => 1, 'député' => 1, 'sénateur' => 1, 'sénatrice' => 1, 'présidente' => 1, 'président' => 1, 'rédaction' => 1, 'issue' => 1, 'spéciale' => 1, 'adopté' => 1, 'lecture'=>1, 'séance'=>1, 'alinéa'=>1, 'résolution'=>1, 'adoption'=>1, 'collègue'=>1, 'cher'=>1, 'collègues'=>1, 'chers'=>1,'bis'=>1, '1er'=>1, 'rectifié'=>1, 'question'=>1, 'rédactionnel'=>1, 'scrutin'=>1, 'exposer'=>1, 'identiques'=>1, 'identique'=>1, 'commission'=>1, 'adopte'=>1, 'rejette' => 1, 'additionnel' => 1, 'tendant' => 1, 'examiné' => 1, 'examine' => 1, 'rejeté'=> 1, 'avis' => 1, 'suivant'=>1, 'estimé'=>1, 'déclaré'=>1);
    $include = array('télévision' => 1, 'dimanche'=>1, 'internet'=>1, 'outre-mer'=>1, 'logement'=>1, 'militaire'=>1, 'taxe'=>1, 'médecin'=>1, 'hôpital'=>1);
    $exclude_sentences = array('commission spéciale' => 1, 'garde des sceaux'=>1, 'haut-commissaire' => 1, 'monsieur' => 1, 'madame'=>1, 'mixte paritaire' => 1, 'commission mixte' => 1, 'commission mixte paritaire' => 1);

    foreach(array_keys($words) as $k) {
      if (!isset($include[$k]))
        $exclude[$k] = 1;
      echo $k.': '.$words[$k]*100/$tot."\n";
      if ($words[$k]*100/$tot < 3)
        break;
    }
    unset($words);
    $q = Doctrine_Query::create();
    $q->select('nom as intervention')->from('Parlementaire o');
    $array = $q->fetchArray();
    $words = $this->count($array);
    foreach(array_keys($words) as $k) {
      $exclude[$k] = 1;
    }
    unset($words);

    $qs = Doctrine::getTable('Seance')->createQuery()->select('id')->where('tagged IS NULL')->orderBy('id'); // date DESC');
//$qs = Doctrine::getTable('Seance')->createQuery()->select('id')->where('id = ?', '1859');

    foreach($qs->fetchArray() as $s) {
      echo "Seance ".$s['id']." ..";

      //Recherche toutes les interventions pour cette séance
      $q = Doctrine_Query::create();
      $q->select('intervention, id, parlementaire_id')->from('Intervention i')->where('seance_id = ?', $s['id'])->andWhere('( i.parlementaire_id IS NOT NULL OR i.personnalite_id IS NOT NULL )')->andWhere('(i.fonction IS NULL OR i.fonction NOT LIKE ? )', 'président%');

      $array = $q->fetchArray();
      if (!count($array)) {
	echo " pas d'intervention trouvée\n";
        continue;
      }
      $words = $this->count($array, 1);
      $cpt = 0;
      $tot = count($words);
      $tags = array();
      //Pour les mots le plus populaires non exclus on les gardes
      foreach(array_keys($words) as $k) {
        $k = trim($k);
        if (!isset($exclude[$k])) {
          $cpt++;
          $pc = $words[$k]*100/$tot;
          if ($pc < 0.8)
            break;
          $tags[$k] = strlen($k);
        }
      }

      $sentences = null;
      $sent2word = null;
      //On cherche des groupes de mots commums à partir des tags trouvés
      foreach ($array as $inter) {
        $i = null;
        $inter['intervention'] = html_entity_decode(str_replace('&nbsp;', ' ', htmlentities($inter['intervention'], ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');

        foreach (array_keys($tags) as $tag) {
          $srctag = preg_replace('/\//', '\/', $tag);
          if (preg_match('/([^\s,\.:>;\(\)«»]*[^,\.:>;\(\)«»]{7}'.$srctag.'[^\s,\.:<\(\)«»]*)/i', $inter['intervention'], $match)) {
            $sent = trim(strtolower($match[1]));
            if (!isset($sentences[$sent]))
	      $sentences[$sent] = 1;
	    else
	      $sentences[$sent]++;
            $sent2word[$sent] = $tag;
          }
          if (preg_match('/([^\s,\.:>;\(\)«»]*'.$srctag.'[^,\.:<\(\)«»]{7}[^\s,\.:<\(\)«»]*)/i', $inter['intervention'], $match)) {
            $sent = trim(strtolower($match[1]));
	    if (!isset($sentences[$sent]))
	      $sentences[$sent] = 1;
	    else
	      $sentences[$sent]++;
            $sent2word[$sent] = $tag;
          }
        }
      }
      //asort($sentences);

      //Si les groupes de mots ont une certernaines popularité, on les garde
      //Au dessus de 70% d'utilisation le tag contenu est supprimé
      $debut_bani = 'à|de|la|ainsi|ensuite';
      if (count($sentences)) {
        foreach (array_keys($sentences) as $sent) {
	  $sent = preg_replace('/[\[\]\(\)]/', '', $sent);
          if (preg_match("/^($debut_bani)/i", $sent) || preg_match("/($debut_bani)$/i", $sent) || preg_match('/\d|amendement|rapporteur|commision|collègue/i', $sent) )
            continue;

          if (preg_match('/^[A-Z][a-z]/', $sent)) {
            unset($tags[$sent2word[$sent]]);
            continue;
          }

          if (preg_match('/^([a-z]{2} |[A-Z]+)/', $sent) || preg_match('/ [a-z]$/i', $sent)) {
            continue;
          }

          if (($sentences[$sent]*100/$tot > 0.8 || ($words[$sent2word[$sent]] && $sentences[$sent]*100/$words[$sent2word[$sent]] > 70))&& $words[$sent2word[$sent]] > 5) {
	    $ok = 1;
	    foreach($exclude_sentences as $excl_sent) {
	      if (preg_match('/'.$excl_sent.'/i', $sent)) {
		$ok = 0;
		break;
	      }
	    }
	    if ($ok)
	      $tags[$sent] = strlen($sent);
            if ($sentences[$sent]*100/$words[$sent2word[$sent]] > 70)
              unset($tags[$sent2word[$sent]]);
          }
        }
      }
      unset($words);
      unset($sentences);
      unset($sent2word);

      print_r($tags);

      //On cherche maintenant les tags dans les interventions pour les associer
      arsort($tags);
      $tagged = 0;
      foreach ($array as $inter) {
	if (!$inter['parlementaire_id'])
	  continue;

        $i = null;
        foreach (array_keys($tags) as $tag) {
          $tag = trim(preg_replace('/^[dl]\'/', '', trim($tag)));
    	  if (preg_match('/'.preg_replace('/\//', '\/', $tag).'/i', $inter['intervention'])) {
            if (!$i)
              $i = Doctrine::getTable('Intervention')->find($inter['id']);
            $i->addTag($tag);
	  }
        }
        if ($i) {
          $tagged = 1;
	  $i->save();
        }
      }
      if ($tagged == 1) {
	$seance = Doctrine::getTable('Seance')->find($s['id']);
	$seance->tagged = 1;
	$seance->save();
      }
      unset($tags);
       unset($array);
      echo " done.";
      unset($s);
      if ($tagged == 0)
        echo " WARNING: No tag found !";
      echo "\n";
    }
  }
}

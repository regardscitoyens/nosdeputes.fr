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
 
  protected function wordize($interventions, $excludeS = 0, $minsize = 1) {
    foreach($interventions as $i) {
      $i = preg_replace('/\([^\)]+\)/', '', $i);
      $i = preg_replace('/&#339;/', 'oe', $i['intervention']);
      foreach(preg_split('/[\s\,\;\.\:\_\(\)\&\#\<\>\']+/i', $i) as $w) {
	if (!preg_match('/^[A-Z]+$/', $w))
	  $w = strtolower($w);
	if (strlen($w)>$minsize && preg_match('/[a-z]/i', $w)) {
	  //	  $s = soundex($w);
	  $s = $w;
	  if (!isset($words[$s])) $words[$s] = 0;
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
    $q->select('intervention')->from('Intervention i')->where('i.parlementaire_id IS NOT NULL');
    echo "count:\n\t";
    $totinters = $q->count();
    echo $totinters."\n";
    $minsize = 2;
    $exclmin = 3;
    if ($totinters < 20000) {
      $minsize = 6;
      $exclmin = 5;
    }
    $interventions = $q->fetchArray();
    $words = $this->wordis($interventions, 0, $minsize);
    $cpt = 0;
    $tot = count($words);

    $exclude = array("ayant" => 1, "grand-chose" => 1, "après-midi" => 1, "au-delà" => 1, "devant" => 1, "octobre" =>1, "novembre" => 1, "action" => 1, "assemblée" => 1, "activité" => 1, "besoin" => 1, "définition" => 1, "adopté" => 1, "député" => 1, "spéciale" => 1, "spécial" => 1, 'rappelle' => 1, 'hong' => 1, "précédente" => 1, "information" => 1, "délai" => 1, 'applicable' => 1, 'expliqué' => 1, 'propose' => 1, 'relative' => 1, 'indique' => 1, 'vingt' => 1, 'janvier' => 1, 'puis' => 1, 'jour' => 1, 'lecture' => 1, 'séance' => 1, 'alinéa'=>1, 'résolution'=>1, 'adoption'=>1, 'collègue'=>1, 'cher'=>1, 'collègues'=>1, 'chers'=>1,'bis'=>1, '1er'=>1, 'rectifié'=>1, 'question'=>1, 'rédactionnel'=>1, 'scrutin'=>1, 'exposer'=>1, 'identiques'=>1, 'identique'=>1, 'commission'=>1, 'adopte'=>1, 'rejette' => 1, 'additionnel' => 1, 'tendant' => 1, 'examiné' => 1, 'examine' => 1, 'rejeté'=> 1, 'avis' => 1, 'suivant'=>1, 'estimé'=>1, 'déclaré'=>1, 'parce'=>1, 'beaucoup'=>1, 'afin'=>1, 'madame'=>1, 'sous'=>1, 'bonne'=>1, 'monsieur'=>1, 'quelle'=>1, 'quinze'=>1, 'lors'=>1, 'là'=>1, 'long'=>1, 'messieurs'=>1, 'ici'=>1, 'trois'=>1, 'êtes'=>1, 'serait'=>1, 'seront'=>1, 'dix'=>1, 'mot'=>1, 'vin'=>1, 'mon'=>1, 'hier'=>1, 'date'=>1, 'cinq'=>1, 'celui'=>1, 'allez' => 1, 'après' => 1, 'bureau' => 1, 'cause' => 1, 'sous-amendement' => 1, 'certaine' => 1, 'chose' => 1, 'code' => 1, 'compris' => 1, 'général' => 1, 'pris' => 1, 'demande' => 1, 'jeudi' => 1, 'jamais' => 1, 'juillet' => 1, 'mois' => 1, 'plusieurs' => 1, 'mardi' => 1, 'mercredi' => 1, 'lundi' => 1, 'quatre' => 1, 'semaine' => 1, 'suppression' => 1, 'semble' => 1, 'souvent' => 1, 'vers' => 1, 'jamais' => 1, 'comité' => 1, 'discussion' => 1, 'liens' => 1, 'lieux' => 1, 'membres' => 1, 'vendredi' => 1, 'dernière' => 1, 'donner' => 1, 'délégation' => 1, 'défendu' => 1, 'défavorable' => 1, 'exemple' => 1, 'favorable' => 1, 'fonction' => 1, 'grand' => 1, 'habitant' => 1, 'haut' => 1, 'juridique' => 1, 'mars' => 1, 'membre' => 1, 'mettre' => 1, 'mise' => 1, 'ministère' => 1, 'mission' => 1, 'niveau' => 1, 'oui' => 1, 'organique' => 1, 'objet' => 1, 'notion' => 1, 'norme' => 1, 'pense' => 1, 'première' => 1, 'prendre' => 1, 'principe' => 1, 'procédure' => 1, 'puisque' => 1, 'rien' => 1, 'sceaux' => 1, 'réponse' => 1, 'spécial' => 1, 'vise' => 1, 'vos' => 1, 'vote' => 1, 'suppresion' => 1, 'urgence' => 1, "rapporteur" => 1, "rapporteure" => 1);
    $include = array('télévision' => 1, 'dimanche'=>1, 'internet'=>1, 'outre-mer'=>1, 'logement'=>1, 'militaire'=>1, 'taxe'=>1, 'médecin'=>1, 'hôpital'=>1);
    $exclude_sentences = array('vice-président' => 1, 'sceaux'=>1, 'commissaire' => 1, 'monsieur' => 1, 'madame'=>1, 'professeur' => 1, 'amendement' => 1, 'règlement' => 1, 'rectificative' => 1, 'rapporteur' => 1);

    //On exclue les mots les plus populaires (en plus des stopwords)
    foreach(array_keys($words) as $k) {
      if (!isset($include[$k]))
        $exclude[$k] = 1;
      echo $k.': '.$words[$k]*100/$tot."\n";
      if ($words[$k]*100/$tot < $exclmin)
        break;
    }
    unset($words);

    //Exclusion des noms des parlementaires
    $q = Doctrine_Query::create();
    $q->select('nom as intervention')->from('Parlementaire o');
    $interventions = $q->fetchArray();
    $words = $this->wordize($interventions, 0, $minsize);
    foreach(array_keys($words) as $k) {
      $exclude[$k] = 1;
    }
    unset($words);

    $qs = Doctrine::getTable('Seance')->createQuery()->select('id')->where('tagged IS NULL');

    //Pour chacune des séances
    foreach($qs->fetchArray() as $s) {
      echo "Seance ".$s['id']." ..";
      
      //Recherche toutes les interventions pour cette séance
      $q = Doctrine_Query::create();
      $q->select('intervention, id, parlementaire_id')->from('Intervention i')->where('seance_id = ?', $s['id'])->andWhere('( i.parlementaire_id IS NOT NULL OR i.personnalite_id IS NOT NULL )')->andWhere('(i.type = ? OR i.fonction IS NULL OR i.fonction NOT LIKE ?)', array('commission', 'président%'));

      $interventions = $q->fetchArray();
      if (!count($interventions)) {
	echo " pas d'intervention trouvée\n";
        continue;
      }
      $words = $this->wordize($interventions, 1, $minsize);
      $cpt = 0;
      $tot = count($words);
      $tags = array();
      //Pour les mots le plus populaires non exclus on les garde
      foreach(array_keys($words) as $k) {
        if (!isset($exclude[$k]) && !preg_match('/-((il|elle)s?|on|ci|le|[nv]ous)$/', $k)) {
          $cpt++;
          $pc = $words[$k]*100/$tot;
          if ($pc < 0.8)
            break;
          $tags[$k] = strlen($k);
        }
      }

      $sentences = null;
      $sent2word = null;
      //On cherche des groupes de mots communs à partir des tags trouvés
      foreach ($interventions as $inter) {
        $i = null;
        foreach (array_keys($tags) as $tag) {
          if (preg_match('/([^\s\,\.\:\>\;\(\)]*[^\,\.\:\>\;\(\)]{6}'.$tag.'[^\s\,\.\:\<\&\(\)]*)/i', $inter['intervention'], $match)) {
            $sent = strtolower($match[1]);
            if (!isset($sentences[$sent])) $sentences[$sent] = 0;
            $sentences[$sent]++;
            $sent2word[$sent] = $tag;
          }
          if (preg_match('/([^\s\,\.\:\>\;\)\)]*'.$tag.'[^\,\.\:\<\&\(\)]{6}[^\s\,\.\:\<\&\(\)]*)/i', $inter['intervention'], $match)) {
            $sent = strtolower($match[1]);
            if (!isset($sentences[$sent])) $sentences[$sent] = 0;
            $sentences[$sent]++;
            $sent2word[$sent] = $tag;
          }
        }
      }
      //asort($sentences);

      //Si les groupes de mots ont une certernaines popularité, on les garde
      //Au dessus de 70% d'utilisation le tag contenu est supprimé
      $debut_bani = 'à|de?s?|l[ea]?|les|ainsi|ensuite';
      if (count($sentences)) {
        foreach (array_keys($sentences) as $sent) {
	  
          if (preg_match("/^($debut_bani)[' ]/i", $sent) || preg_match("/ ($debut_bani)$/i", $sent) || preg_match('/\d|amendement|rapporteure?|mission|collègue/i', $sent) )
            continue;
	  
          if (preg_match('/^[A-Z][a-z]/', $sent)) {
            unset($tags[$sent2word[$sent]]);
            continue;
          }
	  
          if (preg_match('/^([a-z]{2} |[A-Z]+)/', $sent) || preg_match('/ [a-z]$/i', $sent)) {
            continue;
          }
	  
          if (($sentences[$sent]*100/$tot > 0.8 || $sentences[$sent]*100/$words[$sent2word[$sent]] > 70)&& $words[$sent2word[$sent]] > 5) {
	    $ok = 1;
	    foreach(array_keys($exclude_sentences) as $excl_sent) {
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
      foreach ($interventions as $inter) {
	if (!$inter['parlementaire_id'])
	  continue;

        $i = null;
        foreach (array_keys($tags) as $tag) {
    	  if (preg_match('/'.$tag.'/i', $inter['intervention'])) {
            if (!$i)
              $i = Doctrine::getTable('Intervention')->find($inter['id']);
            $i->addTag($tag);
	  }
        }
        if ($i) {
          $tagged = 1;
	  $i->save();
          $i->free();
        }
      }
      if ($tagged == 1) {
	$seance = Doctrine::getTable('Seance')->find($s['id']);
	$seance->tagged = 1;
	$seance->save();
	$seance->free();
      }
      unset($tags);
      unset($interventions);
      echo " done.";
      unset($s);
      if ($tagged == 0)
        echo " WARNING: No tag found !";
      echo "\n";
    }
  }
}

<?php

class tagSeanceTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'tag';
    $this->name = 'Seance';
    $this->briefDescription = 'Tag Seance';
  }
 
  protected function count($array, $excludeS = 0) {
    foreach($array as $i) {
      $i = preg_replace('/\([^\)]+\)/', '', $i);
      $i = preg_replace('/&#339;/', 'oe', $i['intervention']);
      foreach(preg_split('/[\s\,\;\.\:\_\(\)\&\#\<\>\']+/i', $i) as $w) {
	$w = strtolower($w);
	if (strlen($w)>1 && preg_match('/[a-z]/', $w)) {
	  //	  $s = soundex($w);
	  $s = $w;
	  $words[$s]++;
	  if (!$this->sound[$s])
	    $this->sound[$s] = $w;
	}
      }
    }
    foreach(array_keys($words) as $k) {
      if (preg_match('/s$/', $k)) {
	$ks = preg_replace('/s$/', '', $k);
	if ($words[$ks]) {
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
    $q->select('intervention')->from('Intervention i')->leftJoin('i.PersonnaliteInterventions pi')->where('pi.personnalite_id IS NOT NULL');
    echo "count:\n\t";
    echo $q->count()."\n";
    $array = $q->fetchArray();
    $words = $this->count($array);
    $cpt = 0;
    $tot = count($words);

    foreach(array_keys($words) as $k) {
      $exclude[$k] = 1;
      if ($words[$k]*100/$tot < 0.7)
	break;
    }

    $q = Doctrine_Query::create();
    $q->select('nom as intervention')->from('Parlementaire o');
    $array = $q->fetchArray();
    $words = $this->count($array);
    foreach(array_keys($words) as $k) {
      $exclude[$k] = 1;
    }

    $qs = doctrine::getTable('Seance')->createQuery()->select('id')->where('tagged IS NULL');

    foreach($qs->fetchArray() as $s) {

      //Recherche toutes les interventions pour cette séance
      $q = Doctrine_Query::create();
      $q->select('intervention, id')->from('Intervention i')->leftJoin('i.PersonnaliteInterventions pi')->where('seance_id = ?', $s['id']);
      $array = $q->fetchArray();
      if (!count($array))
	continue;
      $words = $this->count($array, 1);
      $cpt = 0;
      $tot = count($words);
      $tags = array();
      //Pour les mots le plus populaires non exclus on les gardes
      foreach(array_keys($words) as $k) {
	if (!$exclude[$k]) {
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
	foreach (array_keys($tags) as $tag) {
	  if (preg_match('/([^\s\,\.\:\>\;\(\)]*[^\,\.\:\>\;\(\)]{6}'.$tag.'[^\s\,\.\:\<\&\(\)]*)/i', $inter['intervention'], $match)) {
	      $sentences[$match[1]]++;
	      $sent2word[$match[1]] = $tag;
	  }
	  if (preg_match('/([^\s\,\.\:\>\;\)\)]*'.$tag.'[^\,\.\:\<\&\(\)]{6}[^\s\,\.\:\<\&\(\)]*)/i', $inter['intervention'], $match)) {
	    $sentences[$match[1]]++;
	    $sent2word[$match[1]] = $tag;
	  }
	}
      }
      if (!$sentences || !count($sentences))
	continue;
      //asort($sentences);

      //Si les groupes de mots ont une certernaines popularité, on les garde
      //Au dessus de 70% d'utilisation le tag contenu est supprimé
      foreach (array_keys($sentences) as $sent) {
	if (preg_match('/^[A-Z][a-z]/', $sent)) {
	  unset($tags[$sent2word[$sent]]);	  
	  continue;
	}
	if (preg_match('/^([a-z]{2} |[A-Z]+)/', $sent) || preg_match('/ [a-z]$/i', $sent)) {
	  continue;
	}
	if (($sentences[$sent]*100/$tot > 0.8 || $sentences[$sent]*100/$words[$sent2word[$sent]] > 70)&& $words[$sent2word[$sent]] > 5) {
	  echo $sent2word[$sent]." ";
	  echo $sentences[$sent]*100/$tot." > 0.8 || ".$sentences[$sent]*100/$words[$sent2word[$sent]]." > 70)&& ".$words[$sent2word[$sent]]."\n";
	  echo "$sent added\n";
	  $tags[$sent] = strlen($sent);
	  if ($sentences[$sent]*100/$words[$sent2word[$sent]] > 70)
	    unset($tags[$sent2word[$sent]]);
	}
      }

      //On cherche maintenant les tags dans les interventions pour les associer
      arsort($tags);
      foreach ($array as $inter) {
	$i = null;
	foreach (array_keys($tags) as $tag) {
	  if (preg_match('/'.$tag.'/i', $inter['intervention'])) {
	    if (!$i) 
	      $i = doctrine::getTable('Intervention')->find($inter['id']);
	    $i->addTag($tag);
	  }
	}
	if ($i) {
	  $i->getSeance()->tagged = 1;
	  $i->getSeance()->save();
	  $i->save();
	}
      }
      echo $s['id']." seance done\n";
    }
  }
}

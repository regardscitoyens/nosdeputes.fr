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
    $q->select('intervention')->from('Intervention i')->leftJoin('i.PersonnaliteInterventions pi')->where('pi.personnalite_id IS NOT NULL');//->andWhere('type = ?', 'loi');
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
      $q = Doctrine_Query::create();
      $q->select('intervention, id')->from('Intervention i')->leftJoin('i.PersonnaliteInterventions pi')->where('seance_id = ?', $s['id']);
      $array = $q->fetchArray();
      if (!count($array))
	continue;
      $words = $this->count($array, 1);
      $cpt = 0;
      $tot = count($words);
      $tags = array();
      foreach(array_keys($words) as $k) {
	if (!$exclude[$k]) {
	  $cpt++;
	  $pc = $words[$k]*100/$tot;
	  if ($pc < 0.8)
	    break;
	  array_push($tags, $k);
	}
      }
      foreach ($array as $inter) {
	$i = null;
	foreach ($tags as $tag) {
	  if (preg_match('/'.$tag.'/i', $inter['intervention'])) {
	    if (!$i) 
	      $i = doctrine::getTable('Intervention')->find($inter['id']);
	    $i->addTag($tag);
	  }
	}
	if ($i)
	  $i->save();
      }
      echo $s['id']." seance done\n";
    }
  }
}

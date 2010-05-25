<?php

class topDeputesTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'top';
    $this->name = 'Deputes';
    $this->briefDescription = 'Top Deputes';
    $this->addArgument('month', sfCommandArgument::OPTIONAL, 'First day of the month you want to add in db', '2009-01-01');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  /**
   * Ordonne la hash table des députés sur une entre ($type) pour en calculer le classement
   **/
  protected function orderDeputes($type, $reverse = 1) {
    $tot = 0;
    foreach(array_keys($this->deputes) as $id) {
      if (!isset($this->deputes[$id][$type]['value'])) 
	$this->deputes[$id][$type]['value'] = 0;
      $ordered[$id] = $this->deputes[$id][$type]['value'];
      $tot++;
    }
    if ($reverse)
      arsort($ordered);
    else
      asort($ordered);
      
    $cpt = 0;
    $last_value = -999;
    $last_cpt = 0;
    foreach(array_keys($ordered) as $id) {
      $cpt++;
      if ($last_value != $this->deputes[$id][$type]['value'])
	$last_cpt = $cpt;
      $this->deputes[$id][$type]['rank'] = $last_cpt;
      $this->deputes[$id][$type]['max_rank'] = $tot;
      $last_value = $this->deputes[$id][$type]['value'];
    }
  }

  protected function executePresence($q)
  {
    $semaines = $q->select('p.id, s.numero_semaine, pr.id, count(s.id)')
      ->from('Parlementaire p, p.Presences pr, pr.Seance s')
      ->groupBy('p.id, s.numero_semaine')
      ->fetchArray();
    foreach ($semaines as $p) {
      foreach($p['Presences'] as $pr) {
	$this->deputes[$p['id']]['semaine']['value']++;
      }
    }
  }
  protected function executeCommissionPresence($q) 
  {
      $parlementaires = $q->select('p.id, count(pr.id)')
	->from('Parlementaire p, p.Presences pr, pr.Seance s')
	->groupBy('p.id')
	->andWhere('s.type = ?', 'commission')
	->fetchArray();
      foreach ($parlementaires as $p) {
	$this->deputes[$p['id']]['commission_presences']['value'] = $p['count'];
      }
  }
  protected function executeCommissionInterventions($q)
  {
    $q->select('p.id, count(i.id)')
      ->from('Parlementaire p, p.Interventions i')
      ->groupBy('p.id')
      ->andWhere('i.type = ?', 'commission');
    $parlementaires = $q->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['commission_interventions']['value'] = $p['count'];
    }
  }
  protected function executeHemicycleInvectives($q) 
  {
      $parlementaires = $q->select('p.id, count(i.id)')
	->from('Parlementaire p, p.Interventions i, i.Seance s')
	->groupBy('p.id')
	->andWhere('s.type = ?', 'hemicycle')
	->andWhere('i.nb_mots <= 20')
	->fetchArray();
      foreach ($parlementaires as $p) {
	$this->deputes[$p['id']]['hemicycle_interventions_courtes']['value'] = $p['count'];
      }
  }
  protected function executeHemicycleInterventions($q)
  {
    $parlementaires = $q->select('p.id, count(i.id)')
      ->from('Parlementaire p, p.Interventions i, i.Seance s')
      ->groupBy('p.id')
      ->andWhere('s.type = ?', 'hemicycle')
      ->andWhere('i.nb_mots > 20')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['hemicycle_interventions']['value'] = $p['count'];
    }
  }

  protected function executeAmendementsSignes($q)
  {
    $parlementaires = $q->select('p.id, count(a.id)')
      ->from('Parlementaire p, p.Amendements a')
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['amendements_signes']['value'] = $p['count'];
    }
  }
 
  protected function executeAmendementsAdoptes($q)
  {
    $parlementaires = $q->select('p.id, count(a.id)')
      ->from('Parlementaire p, p.Amendements a')
      ->groupBy('p.id')
      ->andWhere('a.sort = ?', 'Adopté')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['amendements_adoptes']['value'] = $p['count'];
    }
  }

  protected function executeAmendementsRejetes($q)
  {
    $parlementaires = $q->select('p.id, count(a.id)')
      ->from('Parlementaire p, p.Amendements a')
      ->groupBy('p.id')
      ->andWhere('a.sort = ?', 'Rejeté')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['amendements_rejetes']['value'] = $p['count'];
    }
  }
  
  protected function executeQuestionsEcrites($q)
  {
    $parlementaires = $q->select('p.id, count(q.id)')
      ->from('Parlementaire p, p.QuestionEcrites q')
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['questions_ecrites']['value'] = $p['count'];
    }
  }
  protected function executeQuestionsOrales($q)
  {
    $questions = $q->select('p.id, count(DISTINCT i.seance_id) as count')
      ->from('Parlementaire p, p.Interventions i')
      ->groupBy('p.id')
      ->andWhere('i.type = ?', 'question')
      ->andWhere('i.nb_mots > 40')
      ->andWhere('i.fonction NOT LIKE ?', 'président%')
      ->fetchArray();
    foreach ($questions as $q) {
      $this->deputes[$q['id']]['questions_orales']['value'] = $q['count'];
    }
  }

  protected function executeDeputesInfo() {
    foreach (array_keys($this->deputes) as $id) {
      $dep = doctrine::getTable('Parlementaire')->find($id);
      $this->deputes[$id]['nom']['value'] = $dep->nom;
      $this->deputes[$id]['groupe']['value'] = $dep->groupe_acronyme;
    }
  }

  protected function executeMonth($date) {
    $q = Doctrine_Query::create();

    print "$date ";
    print date('Y-m-d', strtotime("$date +1month"));
    print "\n";

    $qs = clone $q;
    $qs->where('s.date >= ?', date('Y-m-d', strtotime($date)));
    $qs->andWhere('s.date < ?', date('Y-m-d', strtotime("$date +1month")));
    $this->executePresence(clone $qs);
    $this->executeCommissionPresence(clone $qs);

    print "Presence DONE\n";

    $qi = clone $q;
    $qi->where('i.date >= ?', date('Y-m-d', strtotime($date)));
    $qi->andWhere('i.date < ?', date('Y-m-d', strtotime("$date +1month")));
    $this->executeCommissionInterventions(clone $qi);
    $this->executeHemicycleInterventions(clone $qi);
    $this->executeHemicycleInvectives(clone $qi);
    $this->executeQuestionsOrales(clone $qi);
    
    print "Intervention DONE\n";

    $qa = clone $q;
    $qa->where('a.date >= ?', date('Y-m-d', strtotime($date)));
    $qa->andWhere('a.date < ?', date('Y-m-d', strtotime("$date +1month")));
    $this->executeAmendementsSignes(clone $qa);
    $this->executeAmendementsAdoptes(clone $qa);
    $this->executeAmendementsRejetes(clone $qa);

    print "Amendements DONE\n";

    $qq = clone $q;
    $qq->where('q.date >= ?', date('Y-m-d', strtotime($date)));
    $qq->andWhere('q.date < ?', date('Y-m-d', strtotime("$date +1month")));
    $this->executeQuestionsEcrites($qq);
    
    print "Question DONE\n";

    $this->executeDeputesInfo();

    print "Info Deputes DONE\n";
    return ;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $manager = new sfDatabaseManager($this->configuration);

    $this->deputes = array();

    if (isset($arguments['month']) && preg_match('/(\d{4})-(\d{2})-01/', $arguments['month'], $m)) {
      $this->executeMonth($arguments['month']);
      $globale = doctrine::getTable('VariableGlobale')->findOneByChamp('stats_month');
      if (!$globale) {
	$globale = new VariableGlobale();
	$globale->champ = 'stats_month';
	$globale->value = serialize(array());
      }
      $topMonth = unserialize($globale->value);
      $topMonth[$m[1].$m[2]] = $this->deputes;
      $globale->value = serialize($topMonth);
      $globale->save();
      return;
    }

    
    $deputes = Doctrine::getTable('Parlementaire')->createQuery()
      ->where('type = ?', 'depute')
      ->andWhere('fin_mandat IS NULL') 
      ->fetchArray();
    foreach($deputes as $d) {
      $this->deputes[$d['id']]['groupe'] = $d['groupe_acronyme'];
    }

    $q = Doctrine_Query::create()->where('fin_mandat IS NULL');
 
    $qs = clone $q;
    $qs->andWhere('s.date > ?', date('Y-m-d', time()-60*60*24*365));

    
     
    $this->executePresence(clone $qs);
    $this->orderDeputes('semaine');
    
    $this->executeCommissionPresence(clone $qs);
    $this->orderDeputes('commission_presences');
    
    $qi = clone $q;
    $qi->andWhere('i.date > ?', date('Y-m-d', time()-60*60*24*365));

    $this->executeCommissionInterventions(clone $qi);
    $this->orderDeputes('commission_interventions');
    
    $this->executeHemicycleInterventions(clone $qi);
    $this->orderDeputes('hemicycle_interventions');
    

    $this->executeHemicycleInvectives(clone $qi);
    $this->orderDeputes('hemicycle_interventions_courtes');
    
    $qa = clone $q;
    $qa->andWhere('a.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeAmendementsSignes(clone $qa);
    $this->orderDeputes('amendements_signes');
    
    $this->executeAmendementsAdoptes(clone $qa);
    $this->orderDeputes('amendements_adoptes');

    $this->executeAmendementsRejetes(clone $qa);
    $this->orderDeputes('amendements_rejetes', 0);

    $qq = clone $q;
    $qq->where('q.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeQuestionsEcrites($qq);
    $this->orderDeputes('questions_ecrites');

    $this->executeQuestionsOrales(clone $qi);
    $this->orderDeputes('questions_orales');


    $groupes = array();
    foreach(array_keys($this->deputes) as $id) {
      foreach(array_keys($this->deputes[$id]) as $key) {
	$groupes[$this->deputes[$id]['groupe']][$key]['somme'] += $this->deputes[$id][$key]['value'];
	$groupes[$this->deputes[$id]['groupe']][$key]['nb']++;
      }
      unset($this->deputes[$id]['groupe']);
      $depute = Doctrine::getTable('Parlementaire')->find($id);
      $depute->top = serialize($this->deputes[$id]);
      $depute->save();
    }

    $globale = doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes');
    if (!$globale) {
      $globale = new VariableGlobale();
      $globale->champ = 'stats_groupes';
    }
    $globale->value = serialize($groupes);
    $globale->save();

    //On fait la même chose pour les députés ayant un mandat clos.

    $parlementaires = Doctrine_Query::create()->where('fin_mandat IS NOT NULL')
      ->from('Parlementaire p')->execute();
    
    foreach ($parlementaires as $p) {
      $this->depute = array();
      $q = Doctrine_Query::create()->where('p.id = ?', $p->id);
      
      $qs = clone $q;
      $qs->andWhere('(s.date > ? AND s.date < ?)', array(date('Y-m-d', strtotime($p->debut_mandat)), 
       date('Y-m-d', strtotime($p->fin_mandat)), ));
     
      $this->executePresence(clone $qs);
      
      $this->executeCommissionPresence(clone $qs);
    
      $qi = clone $q;
      $qi->andWhere('(i.date > ? AND i.date < ?)', array(date('Y-m-d', strtotime($p->debut_mandat)), 
							 date('Y-m-d', strtotime($p->fin_mandat)), ));
     
      $this->executeCommissionInterventions(clone $qi);
      
      $this->executeHemicycleInterventions(clone $qi);
    

      $this->executeHemicycleInvectives(clone $qi);
    
      $qa = clone $q;
      $qa->andWhere('(a.date > ? AND a.date < ?)', array(date('Y-m-d', strtotime($p->debut_mandat)), 
							 date('Y-m-d', strtotime($p->fin_mandat)), ));
     

      $this->executeAmendementsSignes(clone $qa);
      
      $this->executeAmendementsAdoptes(clone $qa);
      
      $this->executeAmendementsRejetes(clone $qa);
      
      $qq = clone $q;
      $qq->andWhere('(q.date > ? AND q.date < ?)', array(date('Y-m-d', strtotime($p->debut_mandat)), 
							 date('Y-m-d', strtotime($p->fin_mandat)), ));
     
      $this->executeQuestionsEcrites($qq);
      
      $this->executeQuestionsOrales(clone $qi);

      if (count($this->deputes[$p->id])) {
	$p->top = serialize($this->deputes[$p->id]);
	$p->save();
      }
    }
    
  }
}

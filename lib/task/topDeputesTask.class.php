<?php

class topDeputesTask extends sfBaseTask
{

  static $lois = array('Proposition de loi', 'Proposition de résolution');
  
  protected function configure()
  {
    $this->namespace = 'top';
    $this->name = 'Deputes';
    $this->briefDescription = 'Top Deputes';
    $this->addArgument('month', sfCommandArgument::OPTIONAL, 'First day of the month you want to add in db', '');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  /**
   * Ordonne la hash table des députés sur une entre ($type) pour en calculer le classement
   **/
  protected function orderDeputes($type, $reverse = 1) {
    $tot = 0;
    $field = "value";
    if (myTools::isFinLegislature())
      $field = "moyenne";
    foreach(array_keys($this->deputes) as $id) {
      if (!isset($this->deputes[$id][$type][$field])) 
	$this->deputes[$id][$type][$field] = 0;
      $ordered[$id] = $this->deputes[$id][$type][$field];
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
      if ($last_value != $this->deputes[$id][$type][$field])
	$last_cpt = $cpt;
      $this->deputes[$id][$type]['rank'] = $last_cpt;
      $this->deputes[$id][$type]['max_rank'] = $tot;
      $last_value = $this->deputes[$id][$type][$field];
    }
  }

  protected function executeMoyenneMois($field) {
    if (myTools::isFinlegislature() && isset($this->deputes[1]['nb_mois']))
      foreach ($this->deputes as $id => $p) {
        if (!$this->deputes[$id][$field]['value'])
          $this->deputes[$id][$field]['value'] = 0;
        if ($this->deputes[$id]['nb_mois'])
          $this->deputes[$id][$field]['moyenne'] = round(100 * $this->deputes[$id][$field]['value'] / $this->deputes[$id]['nb_mois']) / 100;
        else $this->deputes[$id][$field]['moyenne'] = $this->deputes[$id][$field]['value'];
      }
  }

  protected function executePresence($q)
  {
    $semaines = $q->select('p.id, s.annee, s.numero_semaine, pr.id, count(s.id)')
      ->from('Parlementaire p, p.Presences pr, pr.Seance s')
      ->groupBy('p.id, s.annee, s.numero_semaine')
      ->fetchArray();
    foreach ($semaines as $p) {
      foreach($p['Presences'] as $pr) {
	$this->deputes[$p['id']]['semaines_presence']['value']++;
      }
    }
    $this->executeMoyenneMois('semaines_presence');
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
    $this->executeMoyenneMois('commission_presences');
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
    $this->executeMoyenneMois('commission_interventions');
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
      $this->executeMoyenneMois('hemicycle_interventions_courtes');
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
    $this->executeMoyenneMois('hemicycle_interventions');
  }

  protected function executeAmendementsSignes($q)
  {
    $parlementaires = $q->select('p.id, count(a.id)')
      ->from('Parlementaire p, p.Amendements a')
      ->groupBy('p.id')
      ->andWhere('a.sort != ?', 'Rectifié')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['amendements_signes']['value'] = $p['count'];
    }
    $this->executeMoyenneMois('amendements_signes');
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
    $this->executeMoyenneMois('amendements_adoptes');
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
    $this->executeMoyenneMois('amendements_rejetes');
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
    $this->executeMoyenneMois('questions_ecrites');
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
    $this->executeMoyenneMois('questions_orales');
  }
  protected function executeRapports($q)
  {
    $parlementaires = $q->select('p.id, count(t.id)')
      ->from('Parlementaire p, p.Textelois t')
      ->andWhere('t.type != ? AND t.type != ?', self::$lois)
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['rapports']['value'] = $p['count'];
    }
    $this->executeMoyenneMois('rapports');
  }
  protected function executePropositionsEcrites($q)
  {
    $parlementaires = $q->select('p.id, count(t.id)')
      ->from('Parlementaire p, p.ParlementaireTextelois pt, pt.Texteloi t')
      ->andWhere('t.type = ? OR t.type = ?', self::$lois)
      ->andWhere('pt.importance < ?', 4)
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['propositions_ecrites']['value'] = $p['count'];
    }
    $this->executeMoyenneMois('propositions_ecrites');
  }
  protected function executePropositionsSignees($q)
  {
    $parlementaires = $q->select('p.id, count(t.id)')
      ->from('Parlementaire p, p.ParlementaireTextelois pt, pt.Texteloi t')
      ->andWhere('t.type = ? OR t.type = ?', self::$lois)
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->deputes[$p['id']]['propositions_signees']['value'] = $p['count'];
    }
    $this->executeMoyenneMois('propositions_signees');
  }   


  protected function executeDeputesInfo() {
    foreach (array_keys($this->deputes) as $id) {
      $dep = Doctrine::getTable('Parlementaire')->find($id);
      //Bidouille pour avoir les paramètres dans le bon ordre
      $this->deputes[$id]['01_nom']['value'] = $dep->nom;
      $this->deputes[$id]['02_groupe']['value'] = $dep->groupe_acronyme;
      $this->deputes[$id]['semaines_presence']['value'] += 0;
      $this->deputes[$id]['questions_orales']['value'] += 0;
      $this->deputes[$id]['questions_ecrites']['value'] += 0;
//      $this->deputes[$id]['amendements_rejetes']['value'] += 0;
      $this->deputes[$id]['amendements_signes']['value'] += 0;
      $this->deputes[$id]['amendements_adoptes']['value'] += 0;
      $this->deputes[$id]['rapports']['value'] += 0;
      $this->deputes[$id]['propositions_ecrites']['value'] += 0;
      $this->deputes[$id]['propositions_signees']['value'] += 0;
      $this->deputes[$id]['commission_presences']['value'] += 0;
      $this->deputes[$id]['commission_interventions']['value'] +=  0;
      $this->deputes[$id]['hemicycle_interventions_courtes']['value'] += 0;
      $this->deputes[$id]['hemicycle_interventions']['value'] += 0;
      ksort($this->deputes[$id]);
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
//    $this->executeAmendementsRejetes(clone $qa);

    print "Amendements DONE\n";

    $qq = clone $q;
    $qq->where('q.date >= ?', date('Y-m-d', strtotime($date)));
    $qq->andWhere('q.date < ?', date('Y-m-d', strtotime("$date +1month")));
    $this->executeQuestionsEcrites($qq);
    
    print "Question DONE\n";

    $qd = clone $q;
    $qd->where('t.date >= ?', date('Y-m-d', strtotime($date)));
    $qd->andWhere('t.date < ?', date('Y-m-d', strtotime("$date +1month")));
    $this->executePropositionsEcrites(clone $qd);
    $this->executePropositionsSignees(clone $qd);
    $this->executeRapports(clone $qd);

    print "Documents DONE\n";

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
      $globale = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_month_'.$m[1].'_'.$m[2]);
      if (!$globale) {
	$globale = new VariableGlobale();
	$globale->champ = 'stats_month_'.$m[1].'_'.$m[2];
      }
      $globale->value = serialize($this->deputes);
      $globale->save();
      return;
    }
    $fin = myTools::isFinLegislature();

    $qdeputes = Doctrine::getTable('Parlementaire')->createQuery()
      ->select('id, groupe_acronyme')
      ->where('type = ?', 'depute');
    if (!$fin)
      $qdeputes->andWhere('fin_mandat IS NULL');
    foreach($qdeputes->fetchArray() as $d) {
      if (isset($d['groupe_acronyme']))
        $this->deputes[$d['id']]['groupe'] = $d['groupe_acronyme'];
      else $this->deputes[$d['id']]['groupe'] = "";
      if ($fin)
        $this->deputes[$d['id']]['nb_mois'] = Doctrine::getTable('Parlementaire')->find($d['id'])->getNbMois();
    }

    $q = Doctrine_Query::create();
    if (!$fin)
      $q->where('fin_mandat IS NULL');
 
    $qs = clone $q;
    if (!$fin)
      $qs->andWhere('s.date > ?', date('Y-m-d', time()-60*60*24*365));
    
     
    $this->executePresence(clone $qs);
    $this->orderDeputes('semaines_presence');

    $this->executeCommissionPresence(clone $qs);
    $this->orderDeputes('commission_presences');

    $qi = clone $q;
    if (!$fin)
      $qi->andWhere('i.date > ?', date('Y-m-d', time()-60*60*24*365));

    $this->executeCommissionInterventions(clone $qi);
    $this->orderDeputes('commission_interventions');
    
    $this->executeHemicycleInterventions(clone $qi);
    $this->orderDeputes('hemicycle_interventions');
    

    $this->executeHemicycleInvectives(clone $qi);
    $this->orderDeputes('hemicycle_interventions_courtes');
    
    $qa = clone $q;
    if (!$fin)
      $qa->andWhere('a.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeAmendementsSignes(clone $qa);
    $this->orderDeputes('amendements_signes');
    
    $this->executeAmendementsAdoptes(clone $qa);
    $this->orderDeputes('amendements_adoptes');

//    $this->executeAmendementsRejetes(clone $qa);
//    $this->orderDeputes('amendements_rejetes', 0);

    $qd = clone $q;
    if (!$fin)
      $qd->where('t.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeRapports(clone $qd);
    $this->orderDeputes('rapports');

    $this->executePropositionsEcrites(clone $qd);
    $this->orderDeputes('propositions_ecrites');

    $this->executePropositionsSignees(clone $qd);
    $this->orderDeputes('propositions_signees');

    $qq = clone $q;
    if (!$fin)
      $qq->where('q.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeQuestionsEcrites($qq);
    $this->orderDeputes('questions_ecrites');

    $this->executeQuestionsOrales(clone $qi);
    $this->orderDeputes('questions_orales');


    $groupes = array();
    foreach(array_keys($this->deputes) as $id) {
      if ($this->deputes[$id]['groupe'] != "") {
        foreach(array_keys($this->deputes[$id]) as $key) {
	  $groupes[$this->deputes[$id]['groupe']][$key]['somme'] += $this->deputes[$id][$key]['value'];
	  $groupes[$this->deputes[$id]['groupe']][$key]['nb']++;
        }
      }
      unset($this->deputes[$id]['groupe']);
      $depute = Doctrine::getTable('Parlementaire')->find($id);
      $depute->top = serialize($this->deputes[$id]);
      $depute->save();
    }

    $globale = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes');
    if (!$globale) {
      $globale = new VariableGlobale();
      $globale->champ = 'stats_groupes';
    }
    $globale->value = serialize($groupes);
    $globale->save();

    //On fait la même chose pour les députés ayant un mandat clos si on n'est pas en fin de législature.
   if (!$fin) {
  
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
      
//      $this->executeAmendementsRejetes(clone $qa);
      
      $qq = clone $q;
      $qq->andWhere('(q.date > ? AND q.date < ?)', array(date('Y-m-d', strtotime($p->debut_mandat)), 
							 date('Y-m-d', strtotime($p->fin_mandat)), ));
     
      $this->executeQuestionsEcrites($qq);
      
      $this->executeQuestionsOrales(clone $qi);

      $qd = clone $q;
      $qd->andWhere('(t.date > ? AND t.date < ?)', array(date('Y-m-d', strtotime($p->debut_mandat)),
                                                         date('Y-m-d', strtotime($p->fin_mandat)), ));
      $this->executePropositionsEcrites(clone $qd);
      $this->executePropositionsSignees(clone $qd);
      $this->executeRapports(clone $qd);

      if (count($this->deputes[$p->id])) {
	$p->top = serialize($this->deputes[$p->id]);
	$p->save();
      }
    }
   }

    $date = time();
    $annee = date('Y', $date); $sem = date('W', $date);
    $start = strtotime(myTools::getDebutLegislature());
    $date_debut = date('Y-m-d', $start);
    $annee0 = date('Y', $start); $sem0 = date('W', $start);
    if ($sem >= 52 && date('n', $date) == 1) $sem = 0;
    if ($sem0 >= 52 && $sem <= 1) $sem0 = 0;
    $n_weeks = ($annee - $annee0)*53 + $sem - $sem0;
    $query = Doctrine_Query::create()
      ->select('COUNT(p.id) as nombre, p.id, p.parlementaire_id, s.type, s.annee, s.numero_semaine')
      ->from('Presence p')
      ->leftJoin('p.Seance s')
      ->where('s.date > ?', $date_debut)
      ->groupBy('s.type, s.annee, s.numero_semaine, p.parlementaire_id')
      ->orderBy('s.type, s.annee, s.numero_semaine, nombre');
    $presences_medi = array('commission' => array_fill(1, $n_weeks, 0),
                            'hemicycle' => array_fill(1, $n_weeks, 0),
                            'total' => array_fill(1, $n_weeks, 0));
    $presences = $query->fetchArray();
    $deps = floor(count($presences)/$n_weeks);
    $mid = floor($deps/2)+1;
    $ct = 0;
    foreach ($presences as $presence) {
      $ct++;
      if ($ct % $deps != $mid) continue;
      $n = ($presence['Seance']['annee'] - $annee0)*53 + $presence['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks)
        $presences_medi[$presence['Seance']['type']][$n] = $presence['nombre'];
    }
    unset($presences);
    $query = Doctrine_Query::create()
      ->select('COUNT(p.id) as nombre, p.id, p.parlementaire_id, s.annee, s.numero_semaine')
      ->from('Presence p')
      ->leftJoin('p.Seance s')
      ->where('s.date > ?', $date_debut)
      ->groupBy('s.annee, s.numero_semaine, p.parlementaire_id')
      ->orderBy('s.annee, s.numero_semaine, nombre');
    $presences = $query->fetchArray();
    $deps = floor(count($presences)/$n_weeks);
    $mid = floor($deps/2)+1;
    $ct = 0;
    foreach ($presences as $presence) {
      $ct++;
      if ($ct % $deps != $mid) continue;
      $n = ($presence['Seance']['annee'] - $annee0)*53 + $presence['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) {
        $presences_medi['total'][$n] = $presence['nombre'];
      }
    }
    unset($presences);
    $globale2 = Doctrine::getTable('VariableGlobale')->findOneByChamp('presences_medi');
    if (!$globale2) {
      $globale2 = new VariableGlobale();
      $globale2->champ = 'presences_medi';
    }
    $globale2->value = serialize($presences_medi);
    $globale2->save();

  }
}

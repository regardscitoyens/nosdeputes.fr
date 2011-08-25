<?php

class topSenateursTask extends sfBaseTask
{

  static $lois = array('Proposition de loi', 'Proposition de résolution');
  
  protected function configure()
  {
    $this->namespace = 'top';
    $this->name = 'Senateurs';
    $this->briefDescription = 'Top Senateurs';
    $this->addArgument('month', sfCommandArgument::OPTIONAL, 'First day of the month you want to add in db', '');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  /**
   * Ordonne la hash table des sénateurs sur une entre ($type) pour en calculer le classement
   **/
  protected function orderSenateurs($type, $reverse = 1) {
    $tot = 0;
    foreach(array_keys($this->senateurs) as $id) {
      if (!isset($this->senateurs[$id][$type]['value'])) 
	$this->senateurs[$id][$type]['value'] = 0;
      $ordered[$id] = $this->senateurs[$id][$type]['value'];
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
      if ($last_value != $this->senateurs[$id][$type]['value'])
	$last_cpt = $cpt;
      $this->senateurs[$id][$type]['rank'] = $last_cpt;
      $this->senateurs[$id][$type]['max_rank'] = $tot;
      $last_value = $this->senateurs[$id][$type]['value'];
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
	$this->senateurs[$p['id']]['semaines_presence']['value']++;
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
	$this->senateurs[$p['id']]['commission_presences']['value'] = $p['count'];
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
      $this->senateurs[$p['id']]['commission_interventions']['value'] = $p['count'];
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
	$this->senateurs[$p['id']]['hemicycle_interventions_courtes']['value'] = $p['count'];
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
      $this->senateurs[$p['id']]['hemicycle_interventions']['value'] = $p['count'];
    }
  }

  protected function executeAmendementsSignes($q)
  {
    $parlementaires = $q->select('p.id, count(a.id)')
      ->from('Parlementaire p, p.Amendements a')
      ->groupBy('p.id')
      ->andWhere('a.sort != ?', 'Rectifié')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->senateurs[$p['id']]['amendements_signes']['value'] = $p['count'];
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
      $this->senateurs[$p['id']]['amendements_adoptes']['value'] = $p['count'];
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
      $this->senateurs[$p['id']]['amendements_rejetes']['value'] = $p['count'];
    }
  }
  
  protected function executeQuestionsEcrites($q)
  {
    $parlementaires = $q->select('p.id, count(q.id)')
      ->from('Parlementaire p, p.QuestionEcrites q')
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->senateurs[$p['id']]['questions_ecrites']['value'] = $p['count'];
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
      $this->senateurs[$q['id']]['questions_orales']['value'] = $q['count'];
    }
  }
  protected function executeRapports($q)
  {
    $parlementaires = $q->select('p.id, count(t.id)')
      ->from('Parlementaire p, p.Textelois t')
      ->andWhere('t.type != ? AND t.type != ?', self::$lois)
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->senateurs[$p['id']]['rapports']['value'] = $p['count'];
    }
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
      $this->senateurs[$p['id']]['propositions_ecrites']['value'] = $p['count'];
    }
  }
  protected function executePropositionsSignees($q)
  {
    $parlementaires = $q->select('p.id, count(t.id)')
      ->from('Parlementaire p, p.ParlementaireTextelois pt, pt.Texteloi t')
      ->andWhere('t.type = ? OR t.type = ?', self::$lois)
      ->groupBy('p.id')
      ->fetchArray();
    foreach ($parlementaires as $p) {
      $this->senateurs[$p['id']]['propositions_signees']['value'] = $p['count'];
    } 
  }   


  protected function executeSenateursInfo() {
    foreach (array_keys($this->senateurs) as $id) {
      $dep = Doctrine::getTable('Parlementaire')->find($id);
      //Bidouille pour avoir les paramètres dans le bon ordre
      $this->senateurs[$id]['01_nom']['value'] = $dep->nom;
      $this->senateurs[$id]['02_groupe']['value'] = $dep->groupe_acronyme;
      $this->senateurs[$id]['semaines_presence']['value'] += 0;
      $this->senateurs[$id]['questions_orales']['value'] += 0;
      $this->senateurs[$id]['questions_ecrites']['value'] += 0;
//      $this->senateurs[$id]['amendements_rejetes']['value'] += 0;
      $this->senateurs[$id]['amendements_signes']['value'] += 0;
      $this->senateurs[$id]['amendements_adoptes']['value'] += 0;
      $this->senateurs[$id]['rapports']['value'] += 0;
      $this->senateurs[$id]['propositions_ecrites']['value'] += 0;
      $this->senateurs[$id]['propositions_signees']['value'] += 0;
      $this->senateurs[$id]['commission_presences']['value'] += 0;
      $this->senateurs[$id]['commission_interventions']['value'] +=  0;
      $this->senateurs[$id]['hemicycle_interventions_courtes']['value'] += 0;
      $this->senateurs[$id]['hemicycle_interventions']['value'] += 0;
      ksort($this->senateurs[$id]);
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

    $this->executeSenateursInfo();

    print "Info Sénateurs DONE\n";
    return ;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $manager = new sfDatabaseManager($this->configuration);

    $this->senateurs = array();

    if (isset($arguments['month']) && preg_match('/(\d{4})-(\d{2})-01/', $arguments['month'], $m)) {
      $this->executeMonth($arguments['month']);
      $globale = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_month_'.$m[1]);
      if (!$globale) {
	$globale = new VariableGlobale();
	$globale->champ = 'stats_month_'.$m[1];
	$globale->value = serialize(array());
      }
      $topMonth = unserialize($globale->value);
      $topMonth[$m[1].$m[2]] = $this->senateurs;
      $globale->value = serialize($topMonth);
      $globale->save();
      return;
    }

    
    $senateurs = Doctrine::getTable('Parlementaire')->createQuery()
      ->where('type = ?', 'senateur')
      ->andWhere('fin_mandat IS NULL') 
      ->fetchArray();
    foreach($senateurs as $d) {
      $this->senateurs[$d['id']]['groupe'] = $d['groupe_acronyme'];
    }

    $q = Doctrine_Query::create()->where('fin_mandat IS NULL');
 
    $qs = clone $q;
    $qs->andWhere('s.date > ?', date('Y-m-d', time()-60*60*24*365));

    
     
    $this->executePresence(clone $qs);
    $this->orderSenateurs('semaines_presence');
    
    $this->executeCommissionPresence(clone $qs);
    $this->orderSenateurs('commission_presences');
    
    $qi = clone $q;
    $qi->andWhere('i.date > ?', date('Y-m-d', time()-60*60*24*365));

    $this->executeCommissionInterventions(clone $qi);
    $this->orderSenateurs('commission_interventions');
    
    $this->executeHemicycleInterventions(clone $qi);
    $this->orderSenateurs('hemicycle_interventions');
    

    $this->executeHemicycleInvectives(clone $qi);
    $this->orderSenateurs('hemicycle_interventions_courtes');
    
    $qa = clone $q;
    $qa->andWhere('a.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeAmendementsSignes(clone $qa);
    $this->orderSenateurs('amendements_signes');
    
    $this->executeAmendementsAdoptes(clone $qa);
    $this->orderSenateurs('amendements_adoptes');

//    $this->executeAmendementsRejetes(clone $qa);
//    $this->orderSenateurs('amendements_rejetes', 0);

    $qd = clone $q;
    $qd->where('t.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeRapports(clone $qd);
    $this->orderSenateurs('rapports');

    $this->executePropositionsEcrites(clone $qd);
    $this->orderSenateurs('propositions_ecrites');

    $this->executePropositionsSignees(clone $qd);
    $this->orderSenateurs('propositions_signees');

    $qq = clone $q;
    $qq->where('q.date > ?', date('Y-m-d', time()-60*60*24*365));
    $this->executeQuestionsEcrites($qq);
    $this->orderSenateurs('questions_ecrites');

    $this->executeQuestionsOrales(clone $qi);
    $this->orderSenateurs('questions_orales');


    $groupes = array();
    foreach(array_keys($this->senateurs) as $id) {
      foreach(array_keys($this->senateurs[$id]) as $key) {
	$groupes[$this->senateurs[$id]['groupe']][$key]['somme'] += $this->senateurs[$id][$key]['value'];
	$groupes[$this->senateurs[$id]['groupe']][$key]['nb']++;
      }
      unset($this->senateurs[$id]['groupe']);
      $senateur = Doctrine::getTable('Parlementaire')->find($id);
      $senateur->top = serialize($this->senateurs[$id]);
      $senateur->save();
    }

    $globale = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes');
    if (!$globale) {
      $globale = new VariableGlobale();
      $globale->champ = 'stats_groupes';
    }
    $globale->value = serialize($groupes);
    $globale->save();

    //On fait la même chose pour les sénateurs ayant un mandat clos.

    $parlementaires = Doctrine_Query::create()->where('fin_mandat IS NOT NULL')
      ->from('Parlementaire p')->execute();
    
    foreach ($parlementaires as $p) {
      $this->senateur = array();
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

      if (count($this->senateurs[$p->id])) {
	$p->top = serialize($this->senateurs[$p->id]);
	$p->save();
      }
    }
    
  }
}

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
  protected function orderDeputes($type) {
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

    $cpt = 0;
    $last_value = -999;
    $last_cpt = 0;

    if ($ordered) {
      arsort($ordered);
      foreach(array_keys($ordered) as $id) {
        $cpt++;
        if ($last_value != $this->deputes[$id][$type][$field])
          $last_cpt = $cpt;
        $this->deputes[$id][$type]['rank'] = $last_cpt;
        $this->deputes[$id][$type]['max_rank'] = $tot;
        $last_value = $this->deputes[$id][$type][$field];
      }
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
    $q->select('p.id, s.annee, s.numero_semaine, pr.id, count(s.id)')
      ->from('Parlementaire p, p.Presences pr, pr.Seance s')
      ->groupBy('p.id, s.annee, s.numero_semaine');
    foreach ($q->fetchArray() as $r)
      $this->deputes[$r['id']]['semaines_presence']['value'] += count($r['Presences']);
    $this->executeMoyenneMois('semaines_presence');
  }

  protected function processResults($q, $field, $parlid_field='parlementaire_id', $groupe_field='parlementaire_groupe_acronyme') {
    foreach ($q->fetchArray() as $r) {
      $this->deputes[$r[$parlid_field]][$field]['value'] += $r['count'];
      if ($r[$groupe_field])
        $this->groupes[$r[$groupe_field]][$field] += $r['count'];
    }
    $this->executeMoyenneMois($field);
  }

  protected function executeCommissionPresence($q)
  {
    $q->select('pr.parlementaire_id, pr.parlementaire_groupe_acronyme, count(pr.id)')
      ->from('Presence pr, pr.Parlementaire p, pr.Seance s')
      ->andWhere('s.type = ?', 'commission')
      ->groupBy('p.id, pr.parlementaire_groupe_acronyme');
    $this->processResults($q, 'commission_presences');
  }

  protected function executeCommissionInterventions($q)
  {
    $q->select('i.parlementaire_id, i.parlementaire_groupe_acronyme, count(i.id)')
      ->from('Intervention i, i.Parlementaire p')
      ->andWhere('i.parlementaire_id IS NOT NULL')
      ->andWhere('i.type = ?', 'commission')
      ->groupBy('p.id, i.parlementaire_groupe_acronyme');
    $this->processResults($q, 'commission_interventions');
  }

  protected function executeHemicycleInvectives($q)
  {
    $q->select('i.parlementaire_id, i.parlementaire_groupe_acronyme, count(i.id)')
      ->from('Intervention i, i.Parlementaire p')
      ->andWhere('i.parlementaire_id IS NOT NULL')
      ->andWhere('i.type != ?', 'commission')
      ->andWhere('i.nb_mots <= 20')
      ->groupBy('p.id, i.parlementaire_groupe_acronyme');
    $this->processResults($q, 'hemicycle_interventions_courtes');
  }

  protected function executeHemicycleInterventions($q)
  {
    $q->select('i.parlementaire_id, i.parlementaire_groupe_acronyme, count(i.id)')
      ->from('Intervention i, i.Parlementaire p')
      ->andWhere('i.parlementaire_id IS NOT NULL')
      ->andWhere('i.type != ?', 'commission')
      ->andWhere('i.nb_mots > 20')
      ->groupBy('p.id, i.parlementaire_groupe_acronyme');
    $this->processResults($q, 'hemicycle_interventions');
  }

  protected function executeAmendementsProposes($q)
  {
    $q->select('a.auteur_id, a.auteur_groupe_acronyme, count(a.id)')
      ->from('Amendement a, a.Auteur p')
      ->andWhere('a.auteur_id IS NOT NULL')
      ->andWhere('a.sort != ?', 'Rectifié')
      ->groupBy('p.id, a.auteur_groupe_acronyme');
    $this->processResults($q, 'amendements_proposes', 'auteur_id', 'auteur_groupe_acronyme');
  }

  protected function executeAmendementsProposesAdoptes($q)
  {
    $q->select('a.auteur_id, a.auteur_groupe_acronyme, count(a.id)')
      ->from('Amendement a, a.Auteur p')
      ->andWhere('a.auteur_id IS NOT NULL')
      ->andWhere('a.sort = ?', 'Adopté')
      ->groupBy('p.id, a.auteur_groupe_acronyme');
    $this->processResults($q, 'amendements_proposes_adoptes', 'auteur_id', 'auteur_groupe_acronyme');
  }

  protected function executeAmendementsProposesRejetes($q)
  {
    $q->select('a.auteur_id, a.auteur_groupe_acronyme, count(a.id)')
      ->from('Amendement a, a.Auteur p')
      ->andWhere('a.auteur_id IS NOT NULL')
      ->andWhere('a.sort = ?', 'Rejeté')
      ->groupBy('p.id, a.auteur_groupe_acronyme');
    $this->processResults($q, 'amendements_proposes_rejetes', 'auteur_id', 'auteur_groupe_acronyme');
  }

  protected function executeAmendementsSignes($q)
  {
    $q->select('pa.parlementaire_id, pa.parlementaire_groupe_acronyme, count(pa.id)')
      ->from('ParlementaireAmendement pa, pa.Parlementaire p, pa.Amendement a')
      ->andWhere('a.sort != ?', 'Rectifié')
      ->groupBy('p.id, pa.parlementaire_groupe_acronyme');
    $this->processResults($q, 'amendements_signes');
  }

  protected function executeAmendementsAdoptes($q)
  {
    $q->select('pa.parlementaire_id, pa.parlementaire_groupe_acronyme, count(pa.id)')
      ->from('ParlementaireAmendement pa, pa.Parlementaire p, pa.Amendement a')
      ->andWhere('a.sort = ?', 'Adopté')
      ->groupBy('p.id, pa.parlementaire_groupe_acronyme');
    $this->processResults($q, 'amendements_adoptes');
  }

  protected function executeAmendementsRejetes($q)
  {
    $q->select('pa.parlementaire_id, pa.parlementaire_groupe_acronyme, count(pa.id)')
      ->from('ParlementaireAmendement pa, pa.Parlementaire p, pa.Amendement a')
      ->andWhere('a.sort = ?', 'Rejeté')
      ->groupBy('p.id, pa.parlementaire_groupe_acronyme');
    $this->processResults($q, 'amendements_rejetes');
  }

  protected function executeQuestionsEcrites($q)
  {
    $q->select('q.parlementaire_id, q.parlementaire_groupe_acronyme, count(q.id)')
      ->from('QuestionEcrite q, q.Parlementaire p')
      ->groupBy('p.id, q.parlementaire_groupe_acronyme');
    $this->processResults($q, 'questions_ecrites');
  }

  protected function executeQuestionsOrales($q)
  {
    $q->select('i.parlementaire_id, i.parlementaire_groupe_acronyme, count(distinct(i.section_id))')
      ->from('Intervention i, i.Parlementaire p')
      ->andWhere('i.parlementaire_id IS NOT NULL')
      ->andWhere('i.type = ?', 'question')
      ->andWhere('i.nb_mots > 40')
      ->andWhere('i.fonction NOT LIKE ?', 'président%')
      ->groupBy('p.id, i.parlementaire_groupe_acronyme, i.seance_id');
    $this->processResults($q, 'questions_orales');
  }

  protected function executeRapports($q)
  {
    $q->select('pt.parlementaire_id, pt.parlementaire_groupe_acronyme, count(pt.id)')
      ->from('ParlementaireTexteloi pt, pt.Parlementaire p, pt.Texteloi t')
      ->andWhereNotIn('t.type', self::$lois)
      ->groupBy('p.id, pt.parlementaire_groupe_acronyme');
    $this->processResults($q, 'rapports');
  }

  protected function executePropositionsEcrites($q)
  {
    $q->select('pt.parlementaire_id, pt.parlementaire_groupe_acronyme, count(pt.id)')
      ->from('ParlementaireTexteloi pt, pt.Parlementaire p, pt.Texteloi t')
      ->andWhereIn('t.type', self::$lois)
      ->andWhere('pt.importance < ?', 4)
      ->groupBy('p.id, pt.parlementaire_groupe_acronyme');
    $this->processResults($q, 'propositions_ecrites');
  }

  protected function executePropositionsSignees($q)
  {
    $q->select('pt.parlementaire_id, pt.parlementaire_groupe_acronyme, count(pt.id)')
      ->from('ParlementaireTexteloi pt, pt.Parlementaire p, pt.Texteloi t')
      ->andWhereIn('t.type', self::$lois)
      ->groupBy('p.id, pt.parlementaire_groupe_acronyme');
    $this->processResults($q, 'propositions_signees');
  }


  protected function executeDeputesInfo($start, $end) {
    foreach (array_keys($this->deputes) as $id) {
      $dep = Doctrine::getTable('Parlementaire')->find($id);
      //Bidouille pour avoir les paramètres dans le bon ordre
      $this->deputes[$id]['01_nom']['value'] = $dep->nom;
      $this->deputes[$id]['02_groupe']['value'] = $dep->getGroupeWhen($start, $end);
      $this->deputes[$id]['semaines_presence']['value'] += 0;
      $this->deputes[$id]['questions_orales']['value'] += 0;
      $this->deputes[$id]['questions_ecrites']['value'] += 0;
      $this->deputes[$id]['amendements_proposes']['value'] += 0;
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
    $start = date('Y-m-d', strtotime($date));
    $end = date('Y-m-d', strtotime("$date +1month"));
    echo "$date $enddate\n";

    $q = Doctrine_Query::create();

    foreach(Doctrine::getTable('Parlementaire')->createQuery('p')->select('p.id')->execute() as $d)
      $this->deputes[$d->id] = array();

    $qs = clone $q;
    $qs->where('s.date >= ?', $start);
    $qs->andWhere('s.date < ?', $end);
    $this->executePresence(clone $qs);
    $this->executeCommissionPresence(clone $qs);

    print "Présences DONE\n";

    $qi = clone $q;
    $qi->where('i.date >= ?', $start);
    $qi->andWhere('i.date < ?', $end);
    $this->executeCommissionInterventions(clone $qi);
    $this->executeHemicycleInterventions(clone $qi);
    $this->executeHemicycleInvectives(clone $qi);
    $this->executeQuestionsOrales(clone $qi);

    print "Interventions DONE\n";

    $qa = clone $q;
    $qa->where('a.date >= ?', $start);
    $qa->andWhere('a.date < ?', $end);
    $this->executeAmendementsProposes(clone $qa);
    $this->executeAmendementsSignes(clone $qa);
    $this->executeAmendementsAdoptes(clone $qa);

    print "Amendements DONE\n";

    $qq = clone $q;
    $qq->where('q.date >= ?', $start);
    $qq->andWhere('q.date < ?', $end);
    $this->executeQuestionsEcrites($qq);

    print "Questions DONE\n";

    $qd = clone $q;
    $qd->where('t.date >= ?', $start);
    $qd->andWhere('t.date < ?', $end);
    $this->executePropositionsEcrites(clone $qd);
    $this->executePropositionsSignees(clone $qd);
    $this->executeRapports(clone $qd);

    print "Documents DONE\n";

    $this->executeDeputesInfo($start, $end);

    print "Infos Députés DONE\n";
    return ;
  }


  protected function execute($arguments = array(), $options = array())
  {
    $manager = new sfDatabaseManager($this->configuration);

    $this->deputes = array();
    $this->groupes = array();

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

    foreach(Doctrine::getTable('Parlementaire')->prepareParlementairesTopQuery($fin)->execute() as $d) {
      if ($fin) {
        $vacances = myTools::getVacances();
        $this->deputes[$d->id]['nb_mois'] = $d->getNbMois($vacances);
      } else $this->deputes[$d->id] = array();
    }

    $q = Doctrine_Query::create();
    if (!$fin) {
      $q->andWhere('p.fin_mandat IS NULL OR p.fin_mandat < p.debut_mandat');
      if (!myTools::isFreshLegislature())
        $q->andWhere('p.debut_mandat < ?', date('Y-m-d', time() - myTools::$dixmois));
      $lastyear = date('Y-m-d', time()-60*60*24*365);
    }

    $qs = clone $q;
    if (!$fin) {
      $qs->andWhere('s.date >= ?', $lastyear);
      $qs->andWhere('s.date >= p.debut_mandat');
    }

    $this->executePresence(clone $qs);
    $this->orderDeputes('semaines_presence');

    $this->executeCommissionPresence(clone $qs);
    $this->orderDeputes('commission_presences');

    $qi = clone $q;
    if (!$fin) {
      $qi->andWhere('i.date >= ?', $lastyear);
      $qi->andWhere('i.date >= p.debut_mandat');
    }

    $this->executeCommissionInterventions(clone $qi);
    $this->orderDeputes('commission_interventions');

    $this->executeHemicycleInterventions(clone $qi);
    $this->orderDeputes('hemicycle_interventions');

    $this->executeHemicycleInvectives(clone $qi);
    $this->orderDeputes('hemicycle_interventions_courtes');

    $qa = clone $q;
    if (!$fin) {
      $qa->andWhere('a.date >= ?', $lastyear);
      $qa->andWhere('a.date >= p.debut_mandat');
    }

    $this->executeAmendementsProposes(clone $qa);
    $this->orderDeputes('amendements_proposes');

    $this->executeAmendementsSignes(clone $qa);
    $this->orderDeputes('amendements_signes');

    $this->executeAmendementsAdoptes(clone $qa);
    $this->orderDeputes('amendements_adoptes');

    $qd = clone $q;
    if (!$fin) {
      $qd->andWhere('t.date >= ?', $lastyear);
      $qd->andWhere('t.date >= p.debut_mandat');
    }

    $this->executeRapports(clone $qd);
    $this->orderDeputes('rapports');

    $this->executePropositionsEcrites(clone $qd);
    $this->orderDeputes('propositions_ecrites');

    $this->executePropositionsSignees(clone $qd);
    $this->orderDeputes('propositions_signees');

    $qq = clone $q;
    if (!$fin) {
      $qq->andWhere('q.date >= ?', $lastyear);
      $qq->andWhere('(q.date >= p.debut_mandat)');
    }

    $this->executeQuestionsEcrites($qq);
    $this->orderDeputes('questions_ecrites');

    $this->executeQuestionsOrales(clone $qi);
    $this->orderDeputes('questions_orales');

    foreach(array_keys($this->deputes) as $id) {
      $depute = Doctrine::getTable('Parlementaire')->find($id);
      if ($depute) {
        $depute->top = serialize($this->deputes[$id]);
        $depute->save();
      } else {
        echo "ERREUR: député '$id' non trouvé\n";
        var_dump($this->deputes[$id]);
      }
    }

    // Store les statistiques par groupe politique
    $globale = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes');
    if (!$globale) {
      $globale = new VariableGlobale();
      $globale->champ = 'stats_groupes';
    }
    $globale->value = serialize($this->groupes);
    $globale->save();


    // On fait la même chose sans classement pour les députés ayant un mandat clos ou trop court si on n'est pas en fin de législature.
    if (!$fin) {
      $qparlementaires = Doctrine_Query::create()
        ->from('Parlementaire p')
        ->where('fin_mandat IS NOT NULL AND debut_mandat <= fin_mandat');

      if (!myTools::isFreshLegislature())
        $qparlementaires->orWhere('debut_mandat >= ?', date('Y-m-d', time() - myTools::$dixmois));

      foreach ($qparlementaires->execute() as $p) {
        $this->depute = array();
        $dates = array(date('Y-m-d', strtotime($p->debut_mandat)), date('Y-m-d', strtotime($p->fin_mandat)));

        $q = Doctrine_Query::create()->where('p.id = ?', $p->id);

        $qs = clone $q;
        $qs->andWhere('(s.date > ? AND s.date < ?)', $dates);
        $this->executePresence(clone $qs);
        $this->executeCommissionPresence(clone $qs);

        $qi = clone $q;
        $qi->andWhere('(i.date > ? AND i.date < ?)', $dates);
        $this->executeCommissionInterventions(clone $qi);
        $this->executeHemicycleInterventions(clone $qi);
        $this->executeHemicycleInvectives(clone $qi);

        $qa = clone $q;
        $qa->andWhere('(a.date > ? AND a.date < ?)', $dates);
        $this->executeAmendementsProposes(clone $qa);
        $this->executeAmendementsSignes(clone $qa);
        $this->executeAmendementsAdoptes(clone $qa);

        $qq = clone $q;
        $qq->andWhere('(q.date > ? AND q.date < ?)', $dates);
        $this->executeQuestionsEcrites($qq);

        $this->executeQuestionsOrales(clone $qi);

        $qd = clone $q;
        $qd->andWhere('(t.date > ? AND t.date < ?)', $dates);
        $this->executePropositionsEcrites(clone $qd);
        $this->executePropositionsSignees(clone $qd);
        $this->executeRapports(clone $qd);

        if (count($this->deputes[$p->id])) {
          $p->top = serialize($this->deputes[$p->id]);
          $p->save();
        }
      }
    }


    // Calcule présences médianes pour les graphes
    $date = time();
    $annee = date('o', $date);
    $sem = date('W', $date);
    $start = strtotime(myTools::getDebutLegislature());
    $date_debut = date('Y-m-d', $start);
    $this->annee0 = date('o', $start);
    $this->sem0 = date('W', $start);
    if ($sem >= 52 && date('n', $date) == 1) $sem = 0;
    if ($this->sem0 >= 52 && $sem <= 1) $this->sem0 = 0;
    $this->n_weeks = max(1, ($annee - $this->annee0)*53 + $sem - $this->sem0 + 1);

    $this->presences_medi = array(
      'commission' => array_fill(1, $this->n_weeks, 0),
      'hemicycle' => array_fill(1, $this->n_weeks, 0),
      'total' => array_fill(1, $this->n_weeks, 0)
    );

    $query = Doctrine_Query::create()
      ->select('count(p.id), p.id, p.parlementaire_id, s.annee, s.numero_semaine')
      ->from('Presence p')
      ->leftJoin('p.Seance s')
      ->where('s.date > ?', $date_debut)
      ->groupBy('s.annee, s.numero_semaine, p.parlementaire_id')
      ->orderBy('s.annee, s.numero_semaine, count');

    $q = clone($query);
    $q->andWhere('s.type = ?', 'commission');
    $this->processMediane($q, 'commission');

    $q = clone($query);
    $q->andWhere('s.type = ?', 'hemicycle');
    $this->processMediane($q, 'hemicycle');

    $this->processMediane($query, 'total');

    $globale2 = Doctrine::getTable('VariableGlobale')->findOneByChamp('presences_medi');
    if (!$globale2) {
      $globale2 = new VariableGlobale();
      $globale2->champ = 'presences_medi';
    }
    $globale2->value = serialize($this->presences_medi);
    $globale2->save();
  }

  protected static function getMedianeArray($arr) {
    $n = count($arr);
    // S'il y a eu moins de 150 députés actifs cette semaine là on la banalise
    if ($n < 150) return 0;
    // Autrement on prend le nombre médian de présences sur la semaine parmi les actifs
    return $arr[round($n/2) + 1];
  }

  protected function processMediane($q, $type) {
    $w = array();
    $curn = -1;
    foreach ($q->fetchArray() as $presence) {
      $n = ($presence['Seance']['annee'] - $this->annee0)*53 + $presence['Seance']['numero_semaine'] - $this->sem0 + 1;
      if ($n > $this->n_weeks)
        break;
      if ($n != $curn) {
        if ($curn != -1)
          $this->presences_medi[$type][$curn] = self::getMedianeArray($w);
        $w = array();
        $curn = $n;
      }
      $w[] = $presence['count'];
    }
    $this->presences_medi[$type][$curn] = self::getMedianeArray($w);
  }

}

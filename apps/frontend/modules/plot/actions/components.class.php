<?php

class plotComponents extends sfComponents
{
  public function executeParlementaire() {
  }
 
  public function executeGetParlData() {
    static $seuil_invective = 20;
    $this->data = array();
    if (!isset($this->session) || $this->session === 'legislature') $this->session = 'lastyear';
    $this->data['fin'] = myTools::isFinLegislature() && ($this->session === 'lastyear');
    if ($this->session === 'lastyear') {
      if (!$this->data['fin'] && isset($this->parlementaire->fin_mandat) && $this->parlementaire->fin_mandat > $this->parlementaire->debut_mandat) {
        $date = strtotime($this->parlementaire->fin_mandat);
        $this->data['mandat_clos'] = true;
      } else $date = time();
      $annee = date('Y', $date); $sem = date('W', $date);
      if ($this->data['fin'])
        $last_year = strtotime(myTools::getDebutLegislature());
      else $last_year = $date - 32054400;
      $date_debut = date('Y-m-d', $last_year);
      $annee0 = date('Y', $last_year); $sem0 = date('W', $last_year);
      if ($sem >= 52 && date('n', $date) == 1) $sem = 0;
      if ($sem0 >= 52 && $sem <= 1) $sem0 = 0;
      $n_weeks = ($annee - $annee0)*53 + $sem - $sem0;
#print "$date ; $annee ; $sem ; $last_year ; $annee0 ; $sem0 ; $date_debut ; $n_weeks";
    } else {
      $query4 = Doctrine_Query::create()
        ->select('s.annee, s.numero_semaine')
        ->from('Seance s')
        ->where('s.session = ?', $this->session)
        ->orderBy('s.date ASC');
      $date_debut = $query4->fetchOne();
      $annee0 = $date_debut['annee'];
      $sem0 = $date_debut['numero_semaine'];
      $query4 = Doctrine_Query::create()
        ->select('s.annee, s.numero_semaine')
        ->from('Seance s')
        ->where('s.session = ?', $this->session)
        ->orderBy('s.date DESC');
      $date_fin = $query4->fetchOne();
      $annee = $date_fin['annee'];
      $sem = $date_fin['numero_semaine'];
      $n_weeks = ($annee - $annee0)*53 + $sem - $sem0 + 1;
    }
    if ($this->data['fin']) {
      $this->data['labels'] = $this->getLabelsMois($n_weeks, $annee0, $sem0);
      $this->data['vacances'] = $this->getVacancesAllMandats($n_weeks, $annee0, $sem0, $this->parlementaire->getMandatsLegislature());
    } else {
      $this->data['labels'] = $this->getLabelsSemaines($n_weeks, $annee0, $sem0);
      $this->data['vacances'] = $this->getVacances($n_weeks, $annee0, $sem0, strtotime($this->parlementaire->debut_mandat));
    }

    $query = Doctrine_Query::create()
      ->select('COUNT(p.id) as nombre, p.id,s.type, s.annee, s.numero_semaine')
      ->from('Presence p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('p.Seance s');
    if ($this->session === 'lastyear')
      $query->andWhere('s.date > ?', $date_debut);
    else $query->andWhere('s.session = ?', $this->session);
    $query->groupBy('s.type, s.annee, s.numero_semaine');
    $presences = $query->fetchArray();

    $this->data['n_presences'] = array('commission' => array_fill(1, $n_weeks, 0),
                               'hemicycle' => array_fill(1, $n_weeks, 0));
    foreach ($presences as $presence) {
      $n = ($presence['Seance']['annee'] - $annee0)*53 + $presence['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) $this->data['n_presences'][$presence['Seance']['type']][$n] += $presence['nombre'];
    }
    unset($presences);

    $query2 = Doctrine_Query::create()
      ->select('count(distinct s.id) as nombre, sum(i.nb_mots) as mots, count(i.id) as interv, s.type, s.annee, s.numero_semaine, i.fonction')
      ->from('Intervention i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.nb_mots > ?', $seuil_invective)
      ->leftJoin('i.Seance s');
    if ($this->session === 'lastyear')
      $query2->andWhere('s.date > ?', $date_debut);
    else $query2->andWhere('s.session = ?', $this->session);
    $query2->groupBy('s.type, s.annee, s.numero_semaine');
    $participations = $query2->fetchArray();

    $this->data['n_participations'] = array('commission' => array_fill(1, $n_weeks, 0),
                                    'hemicycle' => array_fill(1, $n_weeks, 0));
    $this->data['n_mots'] = array('commission' => array_fill(1, $n_weeks, 0),
                          'hemicycle' => array_fill(1, $n_weeks, 0));
    foreach ($participations as $participation) {
      $n = ($participation['Seance']['annee'] - $annee0)*53 + $participation['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) {
        $this->data['n_participations'][$participation['Seance']['type']][$n] += $participation['nombre'];
        $this->data['n_mots'][$participation['Seance']['type']][$n] += $participation['mots']/10000;
      }
    }
    unset($participations);
   if (!$this->data['fin']) {
    $query3 = Doctrine_Query::create()
      ->select('count(distinct s.id) as nombre, i.id, s.annee, s.numero_semaine')
      ->from('Intervention i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.type = ?', 'question')
      ->andWhere('i.fonction NOT LIKE ?', 'président%')
      ->andWhere('i.nb_mots > ?', 2*$seuil_invective)
      ->leftJoin('i.Seance s');
    if ($this->session === 'lastyear')
      $query3->andWhere('s.date > ?', $date_debut);
    else $query3->andWhere('s.session = ?', $this->session);
    $query3->groupBy('s.annee, s.numero_semaine');
    $questionsorales = $query3->fetchArray();

    $this->data['n_questions'] = array_fill(1, $n_weeks, 0);
    foreach ($questionsorales as $question) {
      $n = ($question['Seance']['annee'] - $annee0)*53 + $question['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) {
        if ($this->data['n_questions'][$n] == 0)
          $this->data['n_questions'][$n] -= 0.15;
        $this->data['n_questions'][$n] += $question['nombre'];
      }
    }
    unset($questionsorales);
   }

    # Données de présence mediane par semaine
    $this->data['presences_medi'] = array('commission' => array_fill(1, $n_weeks, 0),
                                          'hemicycle'  => array_fill(1, $n_weeks, 0),
                                          'total' => array_fill(1, $n_weeks, 0));
    $presences_medi = Doctrine::getTable('VariableGlobale')->findOneByChamp('presences_medi');
    if ($presences_medi) {
      $prmedi = unserialize($presences_medi->value);
      $debut_legis = strtotime(myTools::getDebutLegislature());
      $an_legis = date('Y', $debut_legis);
      $sem_legis = date('W', $debut_legis);
      if ($sem_legis == 53) { $an_legis++; $sem_legis = 1; }
      $startweek = ($annee0 - $an_legis)*53 + $sem0 - $sem_legis;
      if ($startweek <= 0) {
        $weeks_acti = count($prmedi['commission']);
        for ($i=0; $i < $weeks_acti - 1; $i++) {
          $this->data['presences_medi']['commission'][$n_weeks-$i] = $prmedi['commission'][$weeks_acti-$i];
          $this->data['presences_medi']['hemicycle'][$n_weeks-$i] = $prmedi['hemicycle'][$weeks_acti-$i];
          $this->data['presences_medi']['total'][$n_weeks-$i] = $prmedi['total'][$weeks_acti-$i];
        }
      } else {
        $this->data['presences_medi']['commission'] = array_slice($prmedi['commission'], $startweek, $n_weeks);
        $this->data['presences_medi']['hemicycle'] = array_slice($prmedi['hemicycle'], $startweek, $n_weeks);
        $this->data['presences_medi']['total'] = array_slice($prmedi['total'], $startweek, $n_weeks);
      }
    }

    # Clean interventiosn de ministre hors périodes de mandat
    for($i=1; $i < $n_weeks; $i++)
      if ($this->data['vacances'][$i] == 20) {
        $this->data['n_presences']['hemicycle'][$i] = 0;
        $this->data['n_presences']['commission'][$i] = 0;
        $this->data['n_participations']['hemicycle'][$i] = 0;
        $this->data['n_participations']['commission'][$i] = 0;
        $this->data['n_mots']['hemicycle'][$i] = 0;
        $this->data['n_mots']['commission'][$i] = 0;
        $this->data['presences_medi']['hemicycle'][$i] = 0;
        $this->data['presences_medi']['commission'][$i] = 0;
        $this->data['presences_medi']['total'][$i] = 0;
        if (isset($this->data['n_questions']))
          $this->data['n_questions'][$i] = 0;
      }
  }

  public static function getVacances($n_weeks, $annee0, $sem0, $debut_mandat) {
    
    $n_vacances = array_fill(1, $n_weeks, 0);
    $mandat_an0 = date('Y', $debut_mandat);
    $mandat_sem0 = date('W', $debut_mandat);
    if ($mandat_sem0 == 53) { $mandat_an0++; $mandat_sem0 = 1; }
    $week0 = ($mandat_an0 - $annee0)*53 + $mandat_sem0 - $sem0 + 1;
    for ($n = 0; $n < $week0 ; $n++) 
      $n_vacances[$n] = 20;

    $vacances = Doctrine::getTable('VariableGlobale')->findOneByChamp('vacances');
    if ($vacances) foreach (unserialize($vacances->value) as $vacance) {
      $n = ($vacance['annee'] - $annee0)*53 + $vacance['semaine'] - $sem0 + 1;
      if ($n > 0 && $n < $n_weeks)
        $n_vacances[$n] = 20;
    }
    return $n_vacances;
  }

  public static function getVacancesAllMandats($n_weeks, $annee0, $sem0, $mandats) {
    $n_vacances = array_fill(1, $n_weeks, 0);
    $n = 0;
    $annee = $annee0;
    $sem = $sem0;
    foreach($mandats as $m) {
      if (preg_match("/^(.*);(.*)?$/", $m, $match)) {
        $debut = strtotime($match[1]);
        $mandat_an0 = date('Y', $debut);
        $mandat_sem0 = date('W', $debut);
        if ($mandat_sem0 == 53) { $mandat_an0++; $mandat_sem0 = 1; }
        if ($match[2] != "")
          $fin = strtotime($match[2]);
        else $fin = time();
        $mandat_an1 = date('Y', $fin);
        $mandat_sem1 = date('W', $fin);
        if ($mandat_sem1 == 53) { $mandat_an1++; $mandat_sem1 = 1; }
        while ($n <= $n_weeks && ($annee < $mandat_an0 || ($annee == $mandat_an0 && $sem < $mandat_sem0))) {
          $n_vacances[$n] = 20;
          $sem++;
          if ($sem == 53) { $annee++ ; $sem = 0; }
          $n++;
        }
        while ($n <= $n_weeks && ($annee < $mandat_an1 || ($annee == $mandat_an1 && $sem < $mandat_sem1))) {
          $sem++;
          if ($sem == 53) { $annee++ ; $sem = 0; }
          $n++;
        }
      }
    }
    while ($n <= $n_weeks) {
      $n_vacances[$n] = 20;
      $n++;
    }  
    $vacances = Doctrine::getTable('VariableGlobale')->findOneByChamp('vacances');
    if ($vacances) foreach (unserialize($vacances->value) as $vacance) {
      $n = ($vacance['annee'] - $annee0)*53 + $vacance['semaine'] - $sem0 + 1;
      if ($n > 0 && $n <= $n_weeks)
        $n_vacances[$n] = 20;
    }
    return $n_vacances;
  }

  public static function getLabelsSemaines($n_weeks, $annee, $sem) {
    if ($sem > 1 && $sem <= 51) $an = $annee + 1;
    else $an = $annee;
    $hashmap = array( '3'  => "Jan ".sprintf('%02d', $an-2000), '6'  => " Fév", '10' => " Mar", '15' => "Avr",
                      '19' => " Mai", '24' => "Juin", '28' => "Juil", '33' => "Août",
                      '38' => "Sept", '42' => " Oct", '47' => "Nov", '52' => "Déc");
    $labels = array_fill(1, $n_weeks, "");
    for ($i = 1; $i <= $n_weeks; $i++) {
      $index = $i + $sem; if ($index > 53) $index -= 53;
      if (isset($hashmap[$index]) && !(($index == 3) && ($sem < 3 && $sem > 1))) $labels[$i] = $hashmap[$index];
    }
    if ($sem < 3 && $sem != 0) $labels[54] = "Jan";
    return $labels;
  }

  public static function getLabelsMois($n_weeks, $annee, $sem) {
    $hashmap = array('4'  => "Jan #AN#", '22' => "Mai", '38' => "Sep");
    $labels = array_fill(1, $n_weeks, "");
    for ($i = 1; $i <= $n_weeks; $i++) {
      $index = $i + $sem;
      $an = sprintf('%02d', $annee + floor($index/53) - 2000);
      $index %= 53;
      if (isset($hashmap[$index]) && !(($index == 3) && ($sem < 3 && $sem > 1))) $labels[$i] = str_replace('#AN#', $an, $hashmap[$index]);
    }
    return $labels;
  }

  public function executeNewGroupes() {
  }

  public function executeGetGroupesData() {
    $this->data = array();
    if (!isset($this->type) || $this->type != "all")
      $this->type = "home";
    $this->data['groupes'] = array(); 
    $this->data['couleurs'] = array();
    $colormap = myTools::getGroupesColorMap();
    foreach (array_reverse(myTools::convertYamlToArray(sfConfig::get('app_groupes_actuels', ''))) as $gpe) {
      $this->data['groupes'][$gpe] = array(); 
      $this->data['couleurs'][] = $colormap[$gpe];
    }
    if ($this->type === "home")
      $this->data['titres'] = array("Députés", "Interventions", "Amendements", "Propositions", "Quest. Écrites");
    else $this->data['titres'] = array("", "Interventions", "Longues", "Courtes", "Déposés", "Adoptés", "de Lois", "Écrites", "Orales");
    $n = count($this->data['titres']);
    $stats = unserialize(Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes')->value);
    $query = Doctrine_Query::create()
      ->select('p.groupe_acronyme, sum(a.nb_multiples) as ct')
      ->from('Parlementaire p')
      ->leftJoin('p.ParlementaireAmendements pa')
      ->leftJoin('pa.Amendement a')
      ->where('pa.numero_signataire = ?', 1)
      ->andWhere('a.sort <> ?', 'Rectifié')
      ->groupBy('p.groupe_acronyme');
    if (!myTools::isFinLegislature())
      $query->andWhere('a.date > ?', date('Y-m-d', time()-60*60*24*365));
    $qamdmts = clone($query);
    $amdmts = $qamdmts->fetchArray();
    if ($this->type === "all") {
      $qamdmts2 = clone($query);
      $amdmts2 = $qamdmts2->andWhere('a.sort = ?', "Adopté")
        ->fetchArray();
    }
    $qprops = Doctrine_Query::create()
      ->select('p.groupe_acronyme, count(DISTINCT(t.id)) as ct')
      ->from('Parlementaire p, p.ParlementaireTextelois pt, pt.Texteloi t')
      ->where('pt.importance = 1')
      ->andWhere('t.type LIKE ?', "proposition%")
      ->groupBy('p.groupe_acronyme');
    if (!myTools::isFinLegislature())
      $qprops->andWhere('t.date > ?', date('Y-m-d', time()-60*60*24*365));
    $props = $qprops->fetchArray();
    foreach ($this->data['groupes'] as $groupe => $arr) if (isset($stats[$groupe])) {
      $this->data['groupes'][$groupe][] = $stats[$groupe]['groupe']['nb'];
      if ($this->type === "all") {
        $this->data['groupes'][$groupe][] = $stats[$groupe]['commission_interventions']['somme'];
        $this->data['groupes'][$groupe][] = $stats[$groupe]['hemicycle_interventions']['somme'];
        $this->data['groupes'][$groupe][] = $stats[$groupe]['hemicycle_interventions_courtes']['somme'];
      } else $this->data['groupes'][$groupe][] = $stats[$groupe]['hemicycle_interventions']['somme']+$stats[$groupe]['commission_interventions']['somme'];
      $stats[$groupe]['amdmts'] = 0;
      $stats[$groupe]['amdmts_adoptes'] = 0;
      $stats[$groupe]['propositions'] = 0;
    }
    foreach ($amdmts as $amdt) if ($amdt['groupe_acronyme'] != "")
      $stats[$amdt['groupe_acronyme']]['amdmts'] = $amdt['ct'];
    if ($this->type === "all")
      foreach ($amdmts2 as $amdt) if ($amdt['groupe_acronyme'] != "")
        $stats[$amdt['groupe_acronyme']]['amdmts_adoptes'] = $amdt['ct'];
    foreach ($props as $pro) if ($pro['groupe_acronyme'] != "")
      $stats[$pro['groupe_acronyme']]['propositions'] = $pro['ct'];
    foreach ($this->data['groupes'] as $groupe => $arr) if (isset($stats[$groupe])) {
      $this->data['groupes'][$groupe][] = $stats[$groupe]['amdmts'];
      if ($this->type === "all")
        $this->data['groupes'][$groupe][] = $stats[$groupe]['amdmts_adoptes'];
      $this->data['groupes'][$groupe][] = $stats[$groupe]['propositions'];
      $this->data['groupes'][$groupe][] = $stats[$groupe]['questions_ecrites']['somme'];
      if ($this->type === "all")
        $this->data['groupes'][$groupe][] = $stats[$groupe]['questions_orales']['somme'];
    }
    $this->data['totaux'] = array();
    for ($i=0;$i<$n;$i++)
      $this->data['totaux'][] = 0;
    foreach ($this->data['groupes'] as $groupe => $arr)
      for ($i=0;$i<$n;$i++) if (isset($this->data['groupes'][$groupe][$i]))
        $this->data['totaux'][$i] += $this->data['groupes'][$groupe][$i];
    foreach ($this->data['groupes'] as $groupe => $arr)
      for ($i=0;$i<$n;$i++) if (isset($this->data['groupes'][$groupe][$i]))
        $this->data['groupes'][$groupe][$i] = round($this->data['groupes'][$groupe][$i] / $this->data['totaux'][$i] * 1000)/10;
    for ($i=0;$i<$n;$i++)
      $this->data['totaux'][$i] = preg_replace('/(\d)(\d{3})$/', '\\1 \\2', $this->data['totaux'][$i]);
  }

  public function executeGroupes() {
    $this->empty = 0;
    if (!isset($this->plot)) $this->plot = 'total';
    $this->labels = myTools::convertYamlToArray(sfConfig::get('app_groupes_actuels', '')); 
    $this->couleurs = array();
    $colormap = myTools::getGroupesColorMap();
    foreach ($this->labels as $gpe)
      $this->couleurs[] = $colormap[$gpe];
    $this->interventions = array();
    if ($this->plot == 'total') {
      $this->presences = array();
      $this->interventions_moy = array();
      $this->presences_moy = array();
      $groupes = unserialize(Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes')->value);
      $this->time = 'lastyear';
      foreach($this->labels as $groupe) if ($groupes[$groupe]) {
        $this->presences[] = $groupes[$groupe]['semaines_presence']['somme'];
        $this->presences_moy[] = $groupes[$groupe]['semaines_presence']['somme']/$groupes[$groupe]['groupe']['nb'];
        $this->interventions[] = $groupes[$groupe]['hemicycle_interventions']['somme'];
        $this->interventions_moy[] = $groupes[$groupe]['hemicycle_interventions']['somme']/$groupes[$groupe]['groupe']['nb'];
      }
    } else {
      $groupes = array();
      $this->temps = array();
      if (preg_match('/section_(\d+)$/', $this->plot, $match)) {
        $interventions = Doctrine_Query::create()
	  ->select('p.groupe_acronyme, count(i.id) as count')
	  ->from('Parlementaire p, p.Interventions i, i.Section s')
	  ->where('s.section_id = ?', $match[1])
	  ->andWhere('i.fonction NOT LIKE ?', 'président%')
	  ->andWhere('i.nb_mots > 20')
	  ->groupBy('p.id')
	  ->fetchArray();
        $mots = Doctrine_Query::create()
	  ->select('p.groupe_acronyme, sum(i.nb_mots) as sum')
	  ->from('Parlementaire p, p.Interventions i, i.Section s')
	  ->where('s.section_id = ?', $match[1])
	  ->andWhere('i.fonction NOT LIKE ?', 'président%')
	  ->groupBy('p.id')
	  ->fetchArray();
      } else if (preg_match('/seance_(com|hemi)_(\d+)$/', $this->plot, $match)) {
        if (preg_match('/com/', $this->plot)) $this->seance = $match[2];
        $interventions = Doctrine_Query::create()
          ->select('p.groupe_acronyme, count(i.id) as count')
          ->from('Parlementaire p, p.Interventions i')
          ->where('i.seance_id = ?', $match[2])
          ->andWhere('i.fonction NOT LIKE ?', 'président%')
          ->andWhere('i.nb_mots > 20')
          ->groupBy('p.id')
          ->fetchArray();
        $mots = Doctrine_Query::create()
          ->select('p.groupe_acronyme, sum(i.nb_mots) as sum')
          ->from('Parlementaire p, p.Interventions i')
          ->where('i.seance_id = ?', $match[2])
          ->andWhere('i.fonction NOT LIKE ?', 'président%')
          ->groupBy('p.id')
          ->fetchArray();
        if ($match[1] == 'com') {
          $this->presences = array();
          $presences = Doctrine_Query::create()
            ->select('p.groupe_acronyme, count(pr.id)')
            ->from('Parlementaire p, p.Presences pr, pr.Seance s')
            ->where('s.id = ?', $match[2])
            ->andWhere('s.type = ?', 'commission')
            ->groupBy('p.id')
            ->fetchArray();
          foreach ($presences as $groupe)
            if (!isset($groupes[$groupe['groupe_acronyme']]['presences']))
              $groupes[$groupe['groupe_acronyme']]['presences'] = $groupe['count'];
            else $groupes[$groupe['groupe_acronyme']]['presences'] += $groupe['count'];
        }
      } else if (preg_match('/organisme/', $this->plot, $match) && isset($this->membres)) {
        foreach ($this->membres as $imp => $deps) foreach ($deps as $p)
          if (!isset($groupes[$p['groupe_acronyme']]['membres']))
            $groupes[$p['groupe_acronyme']]['membres'] = 1;
          else $groupes[$p['groupe_acronyme']]['membres'] += 1;
        $this->membres = array();
      } else throw new Exception('wrong plot argument');
      if (isset($interventions) && !count($interventions) && (!isset($presences) || !count($presences))) {
	$this->empty = 1;
	return ;
      }
     if (!isset($this->membres)) {
      foreach ($interventions as $groupe)
        if (!isset($groupes[$groupe['groupe_acronyme']]['interventions']))
          $groupes[$groupe['groupe_acronyme']]['interventions'] = $groupe['count'];
        else $groupes[$groupe['groupe_acronyme']]['interventions'] += $groupe['count'];
      foreach ($mots as $groupe)
      if (!isset($groupes[$groupe['groupe_acronyme']]['temps_parole']))
          $groupes[$groupe['groupe_acronyme']]['temps_parole'] = $groupe['sum'];
        else $groupes[$groupe['groupe_acronyme']]['temps_parole'] += $groupe['sum'];
     }
      foreach($this->labels as $groupe) {
       if (!isset($this->membres)) {
        if (!isset($groupes[$groupe]['interventions']))
          $this->interventions[] = 0;
        else $this->interventions[] = $groupes[$groupe]['interventions'];
        if (!isset($groupes[$groupe]['temps_parole']))
          $this->temps[] = 0;
        else $this->temps[] = $groupes[$groupe]['temps_parole'];
        if (isset($presences)) {
          if (!isset($groupes[$groupe]['presences']))
            $this->presences[] = 0;
          else $this->presences[] = $groupes[$groupe]['presences'];
        }
       } else {
        if (!isset($groupes[$groupe]['membres']))
          $this->membres[] = 0;
        else $this->membres[] = $groupes[$groupe]['membres'];
       }
      }
      $this->labels[] = "";
      $this->interventions[] = array_sum($this->interventions)/2;
      $this->temps[] = array_sum($this->temps)/2;
      if (isset($presences))
        $this->presences[] = array_sum($this->presences)/2;
      if (isset($this->membres))
        $this->membres[] = array_sum($this->membres)/2;
    }
  }
  
}

<?php

class plotComponents extends sfComponents
{
  public function executeParlementaire() {
  }

  public function executeGetParlData() {
    static $seuil_invective = 20;
    $this->data = array();
    if (!isset($this->session)) $this->session = 'lastyear';
    if ($this->session === 'lastyear') {
      if (isset($this->parlementaire->fin_mandat) && $this->parlementaire->fin_mandat > $this->parlementaire->debut_mandat) {
        $date = strtotime($this->parlementaire->fin_mandat);
        $this->data['mandat_clos'] = true;
      } else $date = myTools::getEndDataTime();
      $annee = date('Y', $date); $sem = date('W', $date);
      $last_year = $date - 32054400;
      $date_fin = date('Y-m-d', $date);
      $date_debut = date('Y-m-d', $last_year);
      $annee0 = date('o', $last_year); $sem0 = date('W', $last_year);
      if ($sem > 51 && date('n', $date) == 1) $sem = 0;
      if ($sem < 2 && $annee != date('o', $date)) {
        $annee = date('o', $date);
        $sem0 -= 1;
      }
      $n_weeks = ($annee - $annee0)*53 + $sem - $sem0 + 1;
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
    $this->data['labels'] = $this->getLabelsSemaines($n_weeks, $annee0, $sem0);
    $debutmandat = $this->parlementaire->debut_mandat;
    foreach (unserialize($this->parlementaire->anciens_mandats) as $ancien)
      if (preg_match('#^(\d+)/(\d+)/(\d+) / \d+/\d+/'.$annee.'#', $ancien, $match)) {
        $debutmandat = $match[3].'-'.$match[2].'-'.$match[1];
        break;
      }
    $this->data['vacances'] = $this->getVacances($n_weeks, $annee0, $sem0, strtotime($debutmandat));

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

    $query3 = Doctrine_Query::create()
      ->select('COUNT(q.id) AS nombre, YEAR(IF(ISNULL(q.date_cloture),q.date, greatest(q.date, q.date_cloture))) as annee, WEEKOFYEAR(IF(ISNULL(q.date_cloture),q.date, greatest(q.date, q.date_cloture))) as numero_semaine')
      ->from('Question q')
      ->where('q.type != ?', 'Question écrite')
      ->andWhere('q.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('q.reponse != ?', '')
      ->andWhere('IF(ISNULL(q.date_cloture),q.date, greatest(q.date, q.date_cloture)) >= ?', $date_debut)
      ->andWhere('IF(ISNULL(q.date_cloture),q.date, greatest(q.date, q.date_cloture)) < ?', $date_fin)
      ->groupBy('annee, numero_semaine');



    $questionsorales = $query3->fetchArray();

    $this->data['n_questions'] = array_fill(1, $n_weeks, 0);
    foreach ($questionsorales as $question) {
      $n = ($question['annee'] - $annee0)*53 + $question['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) {
        if ($this->data['n_questions'][$n] == 0)
          $this->data['n_questions'][$n] -= 0.15;
        $this->data['n_questions'][$n] += $question['nombre'];
      }
    }
    unset($questionsorales);
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

 public static function getLabelsSemaines($n_weeks, $annee, $sem) {
    if ($sem > 1 && $sem <= 52) $annee += 1;
    $hashmap = array( '3'  => "Jan ".sprintf('%02d', $annee-2000), '6'  => " Fév", '10' => " Mar", '15' => "Avr",
                      '19' => " Mai", '24' => "Juin", '28' => "Juil", '33' => "Août",
                      '38' => "Sept", '42' => " Oct", '47' => "Nov", '52' => "Déc");
    $labels = array_fill(1, $n_weeks, "");
    for ($i = 1; $i <= $n_weeks; $i++) {
      $index = $i + $sem; if ($index > 53) $index -= 53;
      if (isset($hashmap[$index]) && !(($index == 3) && ($sem < 3 && $sem > 1))) $labels[$i] = $hashmap[$index];
    }
    if ($n_weeks > 54 && $sem < 3 && $sem != 0) $labels[55] = "Jan";
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
      $this->data['titres'] = array("Sénateurs", "Interventions", "Amendements", "Propositions", "Quest. Écrites");
    else $this->data['titres'] = array("", "Interventions", "Longues", "Courtes", "Déposés", "Adoptés", "de Lois", "Écrites", "Orales");
    $n = count($this->data['titres']);
    $stats = unserialize(Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes')->value);
    if (myTools::isDebutMandature())
      $enddate = myTools::getDebutMandature();
    else $enddate = date('Y-m-d', myTools::getEndDataTime()-60*60*24*365);
    $startdate = date('Y-m-d', myTools::getEndDataTime());
    $query = Doctrine_Query::create()
      ->select('p.groupe_acronyme, count(DISTINCT(a.id)) as ct')
      ->from('Parlementaire p')
      ->leftJoin('p.ParlementaireAmendements pa')
      ->leftJoin('pa.Amendement a')
      ->where('pa.numero_signataire = ?', 1)
      ->andWhere('p.groupe_acronyme IS NOT NULL')
      ->andWhere('a.sort <> ?', 'Rectifié')
      ->andWhere('a.date > ?', $enddate)
      ->andWhere('a.date < ?', $startdate)
      ->groupBy('p.groupe_acronyme');
    $qamdmts = clone($query);
    $amdmts = $qamdmts->fetchArray();
    if ($this->type === "all") {
      $qamdmts2 = clone($query);
      $amdmts2 = $qamdmts2->andWhere('a.sort = ?', "Adopté")
        ->fetchArray();
    }
    $props = Doctrine_Query::create()
      ->select('p.groupe_acronyme, count(DISTINCT(t.id)) as ct')
      ->from('Parlementaire p, p.ParlementaireTextelois pt, pt.Texteloi t')
      ->where('t.date > ?', $enddate)
      ->andWhere('t.date < ?', $startdate)
      ->andWhere('pt.importance = 1')
      ->andWhere('t.type LIKE ?', "proposition%")
      ->andWhere('p.groupe_acronyme IS NOT NULL')
      ->groupBy('p.groupe_acronyme')
      ->fetchArray();
    foreach ($this->data['groupes'] as $groupe => $arr) if (isset($stats[$groupe])) {
      $this->data['groupes'][$groupe][] = $stats[$groupe]['groupe']['nb'];
      if ($this->type === "all") {
        $this->data['groupes'][$groupe][] = $stats[$groupe]['commission_interventions']['somme'];
        $this->data['groupes'][$groupe][] = $stats[$groupe]['hemicycle_interventions']['somme'];
        $this->data['groupes'][$groupe][] = $stats[$groupe]['hemicycle_interventions_courtes']['somme'];
      } else $this->data['groupes'][$groupe][] = $stats[$groupe]['hemicycle_interventions']['somme']+$stats[$groupe]['commission_interventions']['somme'];
    }
    foreach ($amdmts as $amdt) if ($amdt['groupe_acronyme'] != "" && isset($this->data['groupes'][$amdt['groupe_acronyme']]))
      $this->data['groupes'][$amdt['groupe_acronyme']][] = $amdt['ct'];
    if ($this->type === "all")
      foreach ($amdmts2 as $amdt) if ($amdt['groupe_acronyme'] != "" && isset($this->data['groupes'][$amdt['groupe_acronyme']]))
        $this->data['groupes'][$amdt['groupe_acronyme']][] = $amdt['ct'];
    foreach ($props as $pro) if ($pro['groupe_acronyme'] != "" && isset($this->data['groupes'][$pro['groupe_acronyme']]))
      $this->data['groupes'][$pro['groupe_acronyme']][] = $pro['ct'];
    foreach ($this->data['groupes'] as $groupe => $arr) if (isset($stats[$groupe])) {
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
    $this->seancenom = 'séance';
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
        $this->seancenom = 'séance';
        if ($match[1] == 'com') {
          $this->seancenom = 'réunion';
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
      } else throw new Exception('wrong plot argument');
      if (!count($interventions) && (!isset($presences) || !count($presences))) {
	$this->empty = 1;
	return ;
      }
      foreach ($interventions as $groupe)
        if (!isset($groupes[$groupe['groupe_acronyme']]['interventions']))
          $groupes[$groupe['groupe_acronyme']]['interventions'] = $groupe['count'];
        else $groupes[$groupe['groupe_acronyme']]['interventions'] += $groupe['count'];
      foreach ($mots as $groupe)
      if (!isset($groupes[$groupe['groupe_acronyme']]['temps_parole']))
          $groupes[$groupe['groupe_acronyme']]['temps_parole'] = $groupe['sum'];
        else $groupes[$groupe['groupe_acronyme']]['temps_parole'] += $groupe['sum'];

      foreach($this->labels as $groupe) {
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
      }
      $this->labels[] = "";
      $this->interventions[] = array_sum($this->interventions)/2;
      $this->temps[] = array_sum($this->temps)/2;
      if (isset($presences))
        $this->presences[] = array_sum($this->presences)/2;

    }
  }

}

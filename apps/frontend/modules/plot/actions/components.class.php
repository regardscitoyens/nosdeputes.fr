<?php

class plotComponents extends sfComponents
{
  public function executeParlementaire() {
    static $seuil_invective = 20;
    if (!isset($this->options)) $this->options = array();
    if (!isset($this->options['plot']) || $this->options['plot'] != 'total')
      $this->sessions = Doctrine_Query::create()
        ->select('s.session')
        ->from('Seance s')
        ->leftJoin('s.Interventions i')
        ->where('i.parlementaire_id = ?', $this->parlementaire->id)
        ->andWhere('s.session IS NOT NULL AND s.session <> ""')
        ->groupBy('s.session')->fetchArray();
    if (!isset($this->options['session'])) $this->options['session'] = 'lastyear';
    if ($this->options['session'] == 'lastyear') {
      if (isset($this->parlementaire->fin_mandat) && $this->parlementaire->fin_mandat > $this->parlementaire->debut_mandat) {
        $date = strtotime($this->parlementaire->fin_mandat);
        $this->mandat_clos = true;
      } else $date = time() - 7*60*60*24;
      $annee = date('Y', $date); $sem = date('W', $date); if ($sem == 53) { $annee++; $sem = 1; }
      $last_year = $date - 31536000;
      if ($this->parlementaire->debut_mandat > date('Y-m-d', $last_year)) {
        $date_debut = $this->parlementaire->debut_mandat;
        $last_year = strtotime($this->parlementaire->debut_mandat);
      } else $date_debut = date('Y-m-d', $last_year);
      $annee0 = date('Y', $last_year); $sem0 = date('W', $last_year); if ($sem0 == 53) { $annee0++; $sem0 = 1; }
      $n_weeks = ($annee - $annee0)*52 + $sem - $sem0 + 1;
    } else {
      $query4 = Doctrine_Query::create()
        ->select('s.annee, s.numero_semaine')
        ->from('Seance s')
        ->where('s.session = ?', $this->options['session'])
        ->orderBy('s.date ASC');
      $date_debut = $query4->fetchOne();
      $annee0 = $date_debut['annee'];
      $sem0 = $date_debut['numero_semaine'] - 1;
      $query4 = Doctrine_Query::create()
        ->select('s.annee, s.numero_semaine')
        ->from('Seance s')
        ->where('s.session = ?', $this->options['session'])
        ->orderBy('s.date DESC');
      $date_fin = $query4->fetchOne();
      $annee = $date_fin['annee'];
      $sem = $date_fin['numero_semaine'] + 1;
      $n_weeks = ($annee - $annee0)*52 + $sem - $sem0 + 1;
    }
    $this->labels = $this->getLabelsSemaines($n_weeks, $annee0, $sem0);
    $this->vacances = $this->getVacances($n_weeks, $annee0, $sem0);

    $query = Doctrine_Query::create()
      ->select('COUNT(p.id) as nombre, p.id,s.type, s.annee, s.numero_semaine')
      ->from('Presence p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('p.Seance s');
    if ($this->options['session'] == 'lastyear')
      $query->andWhere('s.date > ?', $date_debut);
    else $query->andWhere('s.session = ?', $this->options['session']);
    $query->groupBy('s.type, s.annee, s.numero_semaine');
    $presences = $query->fetchArray();

    $this->n_presences = array('commission' => array_fill(1, $n_weeks, 0),
                               'hemicycle' => array_fill(1, $n_weeks, 0));
    foreach ($presences as $presence) {
      $n = ($presence['Seance']['annee'] - $annee0)*52 + $presence['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) $this->n_presences[$presence['Seance']['type']][$n] += $presence['nombre'];
    }
    unset($presences);

    $query2 = Doctrine_Query::create()
      ->select('count(distinct s.id) as nombre, sum(i.nb_mots) as mots, count(i.id) as interv, s.type, s.annee, s.numero_semaine, i.fonction')
      ->from('Intervention i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('i.Seance s');
    if ($this->options['session'] == 'lastyear')
      $query2->andWhere('s.date > ?', $date_debut);
    else $query2->andWhere('s.session = ?', $this->options['session']);
    $query2->groupBy('s.type, s.annee, s.numero_semaine');
    $participations = $query2->fetchArray();

    $this->n_participations = array('commission' => array_fill(1, $n_weeks, 0),
                                    'hemicycle' => array_fill(1, $n_weeks, 0));
    $this->n_mots = array('commission' => array_fill(1, $n_weeks, 0),
                          'hemicycle' => array_fill(1, $n_weeks, 0));
    $this->fonctions = array('commission' => 0, 'hemicycle' => 0);
    foreach ($participations as $participation) {
      $n = ($participation['Seance']['annee'] - $annee0)*52 + $participation['Seance']['numero_semaine'] - $sem0 + 1;
      if ($n <= $n_weeks) {
        if ($participation['mots']/$participation['interv'] > $seuil_invective) {
          $this->n_participations[$participation['Seance']['type']][$n] += $participation['nombre'];
        }
        $this->n_mots[$participation['Seance']['type']][$n] += $participation['mots']/10000;
        if ($participation['fonction'] != "") $this->fonctions[$participation['Seance']['type']] += $participation['nombre'];
      }
    }
    unset($participations);

    if (isset($this->options['questions'])) {
      $query3 = Doctrine_Query::create()
        ->select('count(distinct s.id) as nombre, i.id, s.annee, s.numero_semaine')
        ->from('Intervention i')
        ->where('i.parlementaire_id = ?', $this->parlementaire->id)
        ->andWhere('i.type = ?', 'question')
        ->andWhere('i.fonction NOT LIKE ?', 'président%')
        ->andWhere('i.nb_mots > ?', $seuil_invective)
        ->leftJoin('i.Seance s');
      if ($this->options['session'] == 'lastyear')
        $query3->andWhere('s.date > ?', $date_debut);
      else $query3->andWhere('s.session = ?', $this->options['session']);
      $query3->groupBy('s.annee, s.numero_semaine');
      $questionsorales = $query3->fetchArray();

      $this->n_questions = array_fill(1, $n_weeks, 0);
      foreach ($questionsorales as $question) {
        $n = ($question['Seance']['annee'] - $annee0)*52 + $question['Seance']['numero_semaine'] - $sem0 + 1;
        if ($n <= $n_weeks) {
          if ($this->n_questions[$n] == 0)
            $this->n_questions[$n] -= 0.15;
          $this->n_questions[$n] += $question['nombre'];
        }
      }
      unset($questionsorales);
    }
  }

  public static function getVacances($n_weeks, $annee0, $sem0) {
    $vacances = Doctrine::getTable('VariableGlobale')->findOneByChamp('vacances');
    $n_vacances = array_fill(1, $n_weeks, 0);
    if ($vacances) foreach (unserialize($vacances->value) as $vacance) {
      $n = ($vacance['annee'] - $annee0)*52 + $vacance['semaine'] - $sem0 + 1;
      if ($n > 0 && $n < $n_weeks)
        $n_vacances[$n] = 20;
    }
    return $n_vacances;
 }

 public static function getLabelsSemaines($n_weeks, $annee0, $sem0) {
    $annee = $annee0 + 1;
    $hashmap = array( '4'  => "Jan ".sprintf('%02d', $annee-2000), '9'  => "Fév ", '13' => "Mar", '17' => "Avr ",
                      '21' => "Mai ", '25' => " Juin", '29' => " Juil", '34' => "Août",
                      '38' => " Sept", '43' => "Oct ", '47' => " Nov", '51' => " Déc" );
    $labels = array_fill(1, $n_weeks, "");
    if ($sem0 < 3) $labels[0] = "Jan ".sprintf('%02d', $annee0-2000);
    else for ($i = 1; $i <= $n_weeks; $i++) {
      $index = $i + $sem0; if ($index > 52) $index -= 52;
      if (isset($hashmap[$index]) && !(($index == 3) && ($sem0 < 3))) $labels[$i] = $hashmap[$index];
    }
    return $labels;
  }

  public function executeGroupes() {
    $this->empty = 0;
    if (!isset($this->plot)) $this->plot = 'total';
    $this->labels = array('NI','UMP','NC','SRC','GDR');
    $this->interventions = array();
    if ($this->plot == 'total') {
      $this->presences = array();
      $this->interventions_moy = array();
      $this->presences_moy = array();
      $groupes = unserialize(Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_groupes')->value);
      $this->time = 'lastyear';
      foreach($this->labels as $groupe) if ($groupes[$groupe]) {
        $this->presences[] = $groupes[$groupe]['semaine']['somme'];
        $this->presences_moy[] = $groupes[$groupe]['semaine']['somme']/$groupes[$groupe]['groupe']['nb'];
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
          ->where('i.section_id = ? OR s.section_id = ?', array($match[1], $match[1]))
          ->andWhere('i.fonction NOT LIKE ?', 'président%')
          ->andWhere('i.nb_mots > 20')
          ->groupBy('p.id')
          ->fetchArray();
        $mots = Doctrine_Query::create()
          ->select('p.groupe_acronyme, sum(i.nb_mots) as sum')
          ->from('Parlementaire p, p.Interventions i, i.Section s')
          ->where('i.section_id = ? OR s.section_id = ?', array($match[1], $match[1]))
          ->andWhere('i.fonction NOT LIKE ?', 'président%')
          ->groupBy('p.id')
          ->fetchArray();
      } else if (preg_match('/seance_(com|hemi)_(\d+)$/', $this->plot, $match)) {
        if (preg_match('/com/', $this->plot)) $this->seance = $match[2];
        $interventions = Doctrine_Query::create()
          ->select('p.groupe_acronyme, count(i.id) as count')
          ->from('Parlementaire p, p.Interventions i, i.Section s')
          ->where('i.seance_id = ?', $match[2])
          ->andWhere('i.fonction NOT LIKE ?', 'président%')
          ->andWhere('i.nb_mots > 20')
          ->groupBy('p.id')
          ->fetchArray();
        $mots = Doctrine_Query::create()
          ->select('p.groupe_acronyme, sum(i.nb_mots) as sum')
          ->from('Parlementaire p, p.Interventions i, i.Section s')
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
    }
  }
  
}

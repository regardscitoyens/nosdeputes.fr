<?php

class plotComponents extends sfComponents
{
  public function executePlotParlementairePresence() {
  }
  public function executeParlementairePresenceLastYear() {
    static $seuil_invective = 20;
    if (!isset($this->options)) $this->options = array();
    if (isset($this->parlementaire->fin_mandat) && $this->parlementaire->fin_mandat > $this->parlementaire->debut_mandat)
      $date = strtotime($this->parlementaire->fin_mandat);
    else $date = time();
    $annee = date('Y', $date); $sem = date('W', $date); if ($sem == 53) { $annee++; $sem = 1; }
    $last_year = $date - 31536000;
    if ($this->parlementaire->debut_mandat > date('Y-m-d', $last_year)) {
      $date_debut = $this->parlementaire->debut_mandat;
      $last_year = strtotime($this->parlementaire->debut_mandat);
    } else $date_debut = date('Y-m-d', $last_year);
    $annee0 = date('Y', $last_year); $sem0 = date('W', $last_year); if ($sem0 == 53) { $annee0++; $sem0 = 1; }
    $n_weeks = ($annee - $annee0)*52 + $sem - $sem0 + 1;
    
    $this->labels = $this->getLabelsSemaines($n_weeks, $annee0, $sem0);
    $this->vacances = $this->getVacances($n_weeks, $annee0, $sem0);

    $query = Doctrine_Query::create()
      ->select('COUNT(p.id) as nombre, p.id,s.type, s.annee, s.numero_semaine')
      ->from('Presence p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('p.Seance s')
      ->andWhere('s.date > ?', $date_debut)
      ->groupBy('s.type, s.annee, s.numero_semaine');
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
      ->leftJoin('i.Seance s')
      ->andWhere('s.date > ?', $date_debut)
      ->groupBy('s.type, s.annee, s.numero_semaine');

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
          if ($this->n_participations[$participation['Seance']['type']][$n] = 0)
            $this->n_participations[$participation['Seance']['type']][$n] -= -0.1;
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
        ->andWhere('i.nb_mots > ?', 5*$seuil_invective)
        ->leftJoin('i.Seance s')
        ->andWhere('s.date > ?', $date_debut)
        ->groupBy('s.annee, s.numero_semaine');
      $questionsorales = $query3->fetchArray();

      $this->n_questions = array_fill(1, $n_weeks, 0);
      foreach ($questionsorales as $question) {
        $n = ($question['Seance']['annee'] - $annee0)*52 + $question['Seance']['numero_semaine'] - $sem0 + 1;
        if ($n <= $n_weeks) {
          if ($this->n_questions[$n] == 0)
            $this->n_questions[$n] -= 0.2;
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
      if ($n > 0 && $n <= $n_weeks)
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


  public function executeParlementairePresenceCommissionBySession() {
    $query = Doctrine_Query::create()
      ->select('COUNT(*) as nombre, p.*')
      ->from('Presence p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('p.Seance s')
      ->andWhere('s.type = ?', 'hemicycle')
      ->addSelect('s.session')
      ->orderBy('s.session')
      ->groupBy('s.session');
    $presences = $query->fetchArray();

    $n_sessions = count($presences);
    $this->sessions = range(1, $n_sessions);
    $this->n_presences = array_fill(1, $n_sessions, 0);
    for ($i = 0; $i < $n_sessions; $i++) {
      $this->n_presences[$i+1] += $presences[$i]['nombre'];
    }

    $query2 = Doctrine_Query::create()
      ->select('count(distinct id) as nombre, s.session')
      ->from('Seance s')
      ->where('s.type = ?', 'commission')
      ->leftJoin('s.Interventions i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.fonction NOT LIKE ?', 'president')
      ->andWhere('i.type = ?', 'commission')
      ->orderBy('s.session')
      ->groupBy('s.session');
    $this->participations = $query2->fetchArray();

    $this->n_participations = array_fill(1, $n_sessions, 0);
    for ($i = 0; $i < $n_sessions; $i++) {
      $this->n_participations[$i+1] += $participations[$i]['nombre'];
    }
  }
}
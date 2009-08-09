<?php

class plotComponents extends sfComponents
{
  public function executeParlementairePresenceLastYear() {
    static $seuil_invective = 20;
    $date = time();
    $annee = date('Y', $date);
    $sem = date('W', $date);
    if ($sem == 53) { $annee++; $sem = 1; }
    $last_year = $date - 31536000;
    if ($this->parlementaire->debut_mandat > date('Y-m-d', $last_year)) {
      $last_year = $this->parlementaire->debut_mandat;
      $date_debut = strtotime($this->parlementaire->debut_mandat);
    } else
      $date_debut = date('Y-m-d', $last_year);
    $annee_debut = date('Y', $last_year);
    $sem_debut = date('W', $last_year);
    if ($sem_debut == 53) { $annee_debut++; $sem_debut = 1; }
    $n_weeks = ($annee - $annee_debut)*52 + $sem - $sem_debut + 1;
    $this->semaines = range(1, $n_weeks);

    $query = Doctrine_Query::create()
      ->select('COUNT(*) as nombre, p.*')
      ->from('Presence p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('p.Seance s')
      ->addSelect('s.type, s.annee, s.numero_semaine')
      ->andWhere('s.date > ?', $date_debut)
      ->groupBy('s.type')
      ->groupBy('s.annee')
      ->addGroupBy('s.numero_semaine');
    $presences = $query->fetchArray();

    $this->n_presences_commission = array_fill(1, $n_weeks, 0);
    $this->n_presences_hemicycle = array_fill(1, $n_weeks, 0);
    foreach ($presences as $presence) {
      $a = $presence['Seance']['annee'];
      $s = $presence['Seance']['numero_semaine'];
      if ($s == 53) { $a++; $s = 1; }
      $n = ($a - $annee_debut)*52 + $s - $sem_debut + 1;
      if ($presence['Seance']['type'] == 'hemicycle')
        $this->n_presences_hemicycle[$n] += $presence['nombre'];
      else $this->n_presences_commission[$n] += $presence['nombre'];
    }

    $query2 = Doctrine_Query::create()
      ->select('count(distinct id) as nombre, sum(i.nb_mots) as mots, s.type, s.annee, s.numero_semaine')
      ->from('Seance s')
      ->where('s.date > ?', $date_debut)
      ->leftJoin('s.Interventions i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.fonction NOT LIKE ?', 'president')
      ->andWhere('i.nb_mots > ?', $seuil_invective)
      ->addGroupBy('s.type')
      ->addGroupBy('s.annee')
      ->addGroupBy('s.numero_semaine');
    $participations = $query2->fetchArray();

    $this->n_participations_commission = array_fill(1, $n_weeks, 0);
    $this->n_participations_hemicycle = array_fill(1, $n_weeks, 0);
    $this->n_mots_commission = array_fill(1, $n_weeks, 0);
    $this->n_mots_hemicycle = array_fill(1, $n_weeks, 0);
    foreach ($participations as $participation) {
      $a = $participation['annee'];
      $s = $participation['numero_semaine'];
      if ($s == 53) { $a++; $s = 1; }
      $n = ($a - $annee_debut)*52 + $s - $sem_debut + 1;
      if ($participation['type'] == 'hemicycle') {
        $this->n_participations_hemicycle[$n] += $participation['nombre'];
        $this->n_mots_hemicycle[$n] += $participation['mots']/1000;
      } else {
        $this->n_participations_commission[$n] += $participation['nombre'];
        $this->n_mots_commission[$n] += $participation['mots']/1000;
      }
    }
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
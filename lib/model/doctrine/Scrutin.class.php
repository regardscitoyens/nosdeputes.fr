<?php

class Scrutin extends BaseScrutin
{
  // Date des premiers scrutins où les délégations ne sont pas toutes à FALSE
  // On ne génère pas de preuve de présence à partir des votes avant cette date
  const DEBUT_DELEGATIONS = '2017-10-24';  # date premier solennel avec délégations
  #const DEBUT_DELEGATIONS = '2018-03-20'; # date premier ordinaire avec délégations

  public function getLinkSource() {
    return "http://www2.assemblee-nationale.fr/scrutins/detail/(legislature)/"
         . sfConfig::get('app_legislature', 13)
         . "/(num)/"
         . $this->numero;
  }

  public function setSeance($id_jo) {
    $seance = Doctrine::getTable('Seance')->findOneByIDJO($id_jo);
    if (!$seance) {
      throw new Exception("Aucune séance trouvée avec l'id JO $id_jo");
    }

    $ret = $this->_set('seance_id', $seance->id)
        && $this->_set('date', $seance->date)
        && $this->_set('numero_semaine', $seance->numero_semaine)
        && $this->_set('annee', $seance->annee);

    $seance->free();
    return $ret;
  }

  // Recherche de l'intervention avec un tableau de votants qui correspond
  public function tagIntervention() {
    // Listing des interventions avec un tableau de scrutin
    $inters = Doctrine::getTable('Intervention')
                      ->createQuery('i')
                      ->where('i.seance_id = ?', $this->seance_id)
                      ->andWhere("i.intervention LIKE '%nombre de votants%suffrages exprimés%pour%contre%'")
                      // ->andWhere("i.intervention LIKE '%table class=\"scrutin\"%'")
                      ->orderBy('i.timestamp')
                      ->execute();

    $found = FALSE;
    $info = "votants: {$this->nombre_votants}, pour: {$this->nombre_pours}, contre: {$this->nombre_contres}";

    foreach ($inters as $inter) {
      // Extraction des votants/pours/contres
      $text = $inter->intervention;
      $mv = preg_match('/nombre de votants(?:<\/td><td>|[,\s]*)(\d+)/i', $text, $match_votant);
      $mp = preg_match('/pour l\'(?:adoption|approbation)(?:<\/td><td>|[,\s]*)(\d+)/i', $text, $match_pour);
      $mc = preg_match('/contre(?:<\/td><td>|[,\s])(\d+)/i', $text, $match_contre);

      if ($mv == 0 || $mp == 0 || $mc == 0) {
        echo "WARNING: décomptes intervention {$inter->id} incomplets :\n$text\n";
      } elseif (intval($match_votant[1]) != $this->nombre_votants
             || intval($match_pour[1]) != $this->nombre_pours
             || intval($match_contre[1]) != $this->nombre_contres) {
        $info .= "\n  inter {$inter->id} différente (v:{$match_votant[1]}, p:{$match_pour[1]}, c:{$match_contre[1]})";
      } else {
        $found = TRUE;
        $inter->addTag("scrutin:numero={$this->numero}");
        break;
      }
    }

    if (!$found) {
      $seance = $this->Seance;
      throw new Exception(
          "Scrutin {$this->numero} non trouvé dans les interventions "
        . "de la séance {$seance->id} du {$seance->date} {$seance->moment}\n"
        . "$info"
      );
    }
  }

  public function setDemandeur($demandeur) {
    // TODO? clean demandeur, set demandeur_groupe_acronyme
    return $this->_set('demandeur', $demandeur);
  }

  public function setTitre($titre) {
    // TODO? clean title
    return $this->_set('titre', $titre);
  }

  public function setStats($sort, $nb_votants, $nb_pours, $nb_contres, $nb_abst) {
    return $this->_set('sort', $sort)
        && $this->_set('nombre_votants', $nb_votants)
        && $this->_set('nombre_pours', $nb_pours)
        && $this->_set('nombre_contres', $nb_contres)
        && $this->_set('nombre_abstentions', $nb_abst);
  }

  public function setVotes($parlementaires) {
    foreach ($parlementaires as $id_an => $data) {
      try {
        $parlscrutin = Doctrine::getTable('ParlementaireScrutin')
                               ->findOneByScrutinIDAN($this->id, $id_an);

        if (!$parlscrutin) {
          $parlscrutin = new ParlementaireScrutin();
          if (!$parlscrutin->setParlementaireByIDAN($id_an)
           || !$parlscrutin->setScrutin($this)) {
            throw new Exception('Could not set ParlId/ScrutinId');
          }
        }

        if (!$parlscrutin->_set('parlementaire_groupe_acronyme', $data->groupe)
         || !$parlscrutin->_set('position', $data->position)
         || !$parlscrutin->_set('position_groupe', $data->position_groupe)
         || !$parlscrutin->_set('par_delegation', $data->par_delegation)
         || !$parlscrutin->_set('mise_au_point_position', $data->mise_au_point_position)) {
          throw new Exception("Could not set vote metadata: {$data}");
        }

        if ($this->Seance->date >= self::DEBUT_DELEGATIONS) {
          $parlscrutin->updatePresence();
        }

        $parlscrutin->save();
        $parlscrutin->free();
      } catch (Exception $e) {
        echo "ERREUR scrutin {$this->id}, parl $id_an: {$e->getMessage()}\n";
      }
    }
  }
}

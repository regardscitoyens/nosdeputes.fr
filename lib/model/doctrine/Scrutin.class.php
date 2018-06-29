<?php

class Scrutin extends BaseScrutin
{
  public function getLinkSource() {
    return "http://www2.assemblee-nationale.fr/scrutins/detail/(legislature)/"
         . sfConfig::get('app_legislature', 13)
         . "/(num)/"
         . $this->numero;
  }

  public function setNumero($numero) {
    return $this->_set('numero', $numero);
  }

  public function setSeance($id_jo) {
    $seance = Doctrine::getTable('Seance')->findOneByIDJO($id_jo);
    if (!$seance) {
      throw new Exception("Aucune séance trouvée avec l'id JO $id_jo");
    }
    $seance_id = $seance->id;
    $seance->free();

    $ret = $this->_set('seance_id', $seance_id)
        && $this->_set('date', $seance->date)
        && $this->_set('numero_semaine', $seance->numero_semaine)
        && $this->_set('annee', $seance->annee);

  }

  public function tagInterventions() {
    // Comptage des scrutins avec numéro inférieur dans la même séance
    $avant = $this->createQuery('s')
                  ->select('count(1) as cnt')
                  ->where('s.seance_id = ?', $this->seance_id)
                  ->andWhere('s.numero < ?', $this->numero)
                  ->fetchOne()['cnt'];

    // Recherche de l'intervention avec un tableau de votants qui correspond
    $inter = Doctrine::getTable('Intervention')
                     ->createQuery('i')
                     ->where('i.seance_id = ?', $this->seance_id)
                     ->andWhere("i.intervention LIKE '%table class=\"scrutin\"%'")
                     ->orderBy('i.timestamp')
                     ->offset($avant)
                     ->getFirst();

    $found = FALSE;
    if ($inter) {
      $found = TRUE;

      // Vérification du nombre de pour/contre
      $text = $inter->intervention;
      if (preg_match('/pour[^<]*<\/td><td>(\d+)/', $text, &$match) == 0
       || intval($match[0]) != $this->nombre_pours
       || preg_match('/contre[^<]*<\/td><td>(\d+)/', $text, &$match) == 0
       || intval($match[0]) != $this->nombre_contres) {
        $found = FALSE;
      } else {
        $inter->addTag("scrutin:numero={$this->numero}");
      }
    }

    if (!$found) {
      throw new Exception("Scrutin {$this->numero} non trouvé dans les interventions de la séance $seance_id");
    }
  }

  public function setDemandeur($demandeur) {
    // TODO? clean demandeur, set demandeur_groupe_acronyme
    return $this->_set('demandeur', $demandeur);
  }

  public function setTitre($titre) {
    return $this->_set('titre', $titre);
  }

  public function setType($type) {
    return $this->_set('type', $type);
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
          if (!$parlscrutin->setParlementaire($id_an) ||
           || !$parlscrutin->setScrutin($this)) {
            return FALSE;
          }
        }

        if (!$parlscrutin->_set('parlementaire_groupe_acronyme', $data->groupe)
         || !$parlscrutin->_set('position', $data->position)
         || !$parlscrutin->_set('position_groupe', $data->position_groupe)
         || !$parlscrutin->_set('par_delegation', $data->par_delegation)
         || !$parlscrutin->_set('mise_au_point_position', $data->mise_au_point_position or NULL)) {
          return FALSE;
        }

        $parlscrutin->updatePresence();
        $parlscrutin->save();
        $parlscrutin->free();
      } catch (Exception $e) {
        echo "ERREUR scrutin {$this->id}, parl $id_an: {$e->getMessage()}\n";
      }
    }
  }
}

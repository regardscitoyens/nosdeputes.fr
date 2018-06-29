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

  public function setSeance($idseance) {
    $seance = Doctrine::getTable('Seance')->findOneByTODO($idseance);
    if (!$seance) {
      throw new Exception("Aucune séance trouvée avec l'id $idseance");
    }

    $ret = $this->_set('seance_id', $seance->id)
        && $this->_set('date', $seance->date)
        && $this->_set('numero_semaine', $seance->numero_semaine)
        && $this->_set('annee', $seance->annee);

    $seance->free();
    return $ret;
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

<?php

class ParlementaireScrutin extends BaseParlementaireScrutin
{

  public function setParlementaire($id_an) {
    $parl = Doctrine::getTable('Parlementaire')->findOneByIdAn($id_an);
    if (!$parl) {
      throw new Exception("Aucun parlementaire trouvé avec l'ID AN $id_an");
    }

    return $this->_set('parlementaire_id', $parl->id);
  }

  public function setScrutin($scrutin) {
    return $this->_set('scrutin_id', $scrutin->id);
  }

  public function updatePresence()
    $this->Scrutin->Seance->setUnsetPresenceLight(
      $this->parlementaire_id,
      $this->groupe_acronyme,
      'scrutin',
      $this->Scrutin->getLinkSource(),
      $this->position != 'nonVotant' && $this->mise_au_point_position == NULL
    );
  }

}

<?php

class ParlementaireScrutin extends BaseParlementaireScrutin
{

  public function setParlementaireByIDAN($id_an) {
    $parl = Doctrine::getTable('Parlementaire')->findOneByIdAn($id_an);
    if (!$parl) {
      throw new Exception("Aucun parlementaire trouvé avec l'ID AN $id_an");
    }

    return $this->_set('parlementaire_id', $parl->id);
  }

  public function updatePresence() {
    $this->Scrutin->Seance->setUnsetPresenceLight(
      $this->parlementaire_id,
      $this->parlementaire_groupe_acronyme,
      'scrutin',
      $this->Scrutin->getLinkSource(),
      $this->position && !$this->par_delegation
    );
  }

}

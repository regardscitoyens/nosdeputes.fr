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
    // on compte une présence quand toutes les conditions suivantes sont vérifiées :
    // cf https://github.com/regardscitoyens/nosdeputes.fr/pull/115#issuecomment-418172211
    // - on a effectivement une position et pas seulement une mise au point
    // - la position n'est pas nonVotant (ce qui correspond aux présidents de Séance + systématiquement au président de l'Assemblée même en son absence cf https://github.com/regardscitoyens/nosdeputes.fr/pull/115#issuecomment-413740999 )
    // - l'éventuelle mise au point n'indique pas non plus nonVotant
    // - le vote n'était pas par délégation
      $this->position && $this->position !== "nonVotant" && $this->mise_au_point_position !== "nonVotant" && !$this->par_delegation
    );
  }

}

<?php


class ScrutinTable extends Doctrine_Table
{

    public static function getInstance()
    {
        return Doctrine_Core::getTable('Scrutin');
    }

    // Date début délégations (cf https://github.com/regardscitoyens/nosdeputes.fr/pull/115#issuecomment-421844588 )
    // On ne génère pas de preuve de présence à partir des votes avant cette date sauf si le scrutin a des délégations (3 cas particuliers de solennel)
    // Avec le covid, les votes par délégations ont été systématisés et leur publication a été arrêtée
    const DELEGATIONS_INDEX_DEBUT = 0;
    const DELEGATIONS_INDEX_FIN = 1;
    public function getDelegationsRanges() {
        return array(
            array('2018-03-20', '2020-03-10'), //début de la publication des délégations de vote
            array('2020-06-29', '9999-99-99')  //suspension pendant le covid de la publication des délégations
        );
    }

}

<?php


class ScrutinTable extends Doctrine_Table
{

    public static function getInstance()
    {
        return Doctrine_Core::getTable('Scrutin');
    }

    // Date début délégations (cf https://github.com/regardscitoyens/nosdeputes.fr/pull/115#issuecomment-421844588 )
    // On ne génère pas de preuve de présence à partir des votes avant cette date sauf si le scrutin a des délégations (3 cas particuliers de solennel)
    const DEBUT_DELEGATIONS = '2018-03-20';
    // Anticipe potentiel recul de la transparence en matière de publicité des délégations
    const FIN_DELEGATIONS = '9999-99-99';
    const DELEGATIONS_INDEX_DEBUT = 0;
    const DELEGATIONS_INDEX_FIN = 1;
    public function getDelegationsRanges() {
        return array(
            array(self::DEBUT_DELEGATIONS, self::FIN_DELEGATIONS),
        );
    }

}

<?php


class ParlementaireScrutinTable extends Doctrine_Table
{

  public static function getInstance()
  {
    return Doctrine_Core::getTable('ParlementaireScrutin');
  }

  public function findOneByScrutinIDAN($scrutin_id, $id_an) {
    $parl = Doctrine::getTable('Parlementaire')->findOneByIdAn($id_an);
    
    if (!$parl) {
      throw new Exception("Aucun parlementaire trouvé avec l'ID AN $id_an");
    }

    $query = $this->createQuery('ps')
                  ->where('ps.scrutin_id = ?', $scrutin_id)
                  ->andWhere('ps.parlementaire_id = ?', $parl->id);
    $res = $query->fetchOne();
    $query->free();
    $parl->free();

    return $res;
  }

}

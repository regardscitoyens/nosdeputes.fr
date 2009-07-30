<?php

/**
 * parlementaire actions.
 *
 * @package    cpc
 * @subpackage parlementaire
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class plotComponents extends sfComponents
{
  public function executeParlementaire()
  {
    $query = Doctrine_Query::create()
        ->select('COUNT(*) as nombre, p.*')
        ->from('Presence p')
        ->where('p.parlementaire_id = ?', $this->parlementaire->id)
        ->leftJoin('p.Seance s')
        ->addSelect('s.annee, s.numero_semaine')
        ->orderBy('s.annee DESC')
        ->addOrderBy('s.numero_semaine DESC')
        ->groupBy('s.annee')
        ->addGroupBy('s.numero_semaine');
    $this->presences = $query->fetchArray();
  }

}

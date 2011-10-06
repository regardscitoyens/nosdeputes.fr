<?php

/**
 * organisme actions.
 *
 * @package    cpc
 * @subpackage photos
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class photosActions extends sfActions
{

  public function executeFlip(sfWebRequest $request) {
    $ids = "";
    $total = Doctrine_Query::create()->select('max(id)')->from('Parlementaire')->fetchOne();
    for ($i=1; $i <= $total['max']; $i++) if ($flip = $request->getParameter('flip'.$i)) {
      if ($ids != "") $ids .= ",";
      $ids .= ','.$i;
    }
    if ($ids) $query = Doctrine_Query::create()
      ->update('Parlementaire')
      ->set('autoflip', 1)
      ->whereIn('id', explode(',', $ids))
      ->execute();
    $this->parlementaires = Doctrine::getTable('Parlementaire')
      ->createQuery()
      ->orderBy('nom_de_famille')
      ->execute();
  }

}

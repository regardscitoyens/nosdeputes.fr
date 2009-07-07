<?php

/**
 * parlementaire actions.
 *
 * @package    cpc
 * @subpackage parlementaire
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class parlementaireActions extends sfActions
{
  public function executeShow(sfWebRequest $request)
  {
    $perma = $request->getParameter('permalink');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneByPermalink($perma);
  }
  public function executeList(sfWebRequest $request)
  {
    $this->pager = new sfDoctrinePager('Parlementaire',
				       20
				       );
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }
}

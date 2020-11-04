<?php
/**
 * scrutins components.
 *
 * @package    cpc
 * @subpackage scrutin
 */
class scrutinActions extends sfActions
{
  public function executeParlementaire(sfWebRequest $request) {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
  }
}
<?php

/**
 * test actions.
 *
 * @package    CIS
 * @subpackage test
 * @author     Michał Organek / XSolve
 * @version    2009-02-09
 */
class xsPChartActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('xsPChart', 'test');
  }

 /**
  * Executes test action
  *
  * @param sfRequest $request A request object
  */
  public function executeTest(sfWebRequest $request)
  {
  }
}

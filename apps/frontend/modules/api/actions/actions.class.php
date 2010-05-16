<?php

/**
 * api actions.
 *
 * @package    cpc
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class apiActions extends sfActions
{
  public function executeSynthese(sfWebRequest $request)
  {
    
  }
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeTop(sfWebRequest $request)
  {
    $date = $request->getParameter('date');
    $vg = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_month');
    $top = unserialize($vg->value);

    $this->forward404Unless(isset($top[$date]));

    $this->setLayout(false);
    $this->res = array();
    foreach(array_keys($top[$date]) as $id) {
      $depute['id'] = $id;
      foreach (array_keys($top[$date][$id]) as $k) {
	$depute[$k] = $top[$date][$id][$k]['value'];
      }
      $this->res["deputes"][] = array('depute' => $depute);
    }
    
    $this->templatize($request->getParameter('type'));
  }
  private function templatize($type) {
    switch($type) {
      case 'json':
	$this->setTemplate('json');
	$this->getResponse()->setContentType('text/plain');
	break;
      case 'xml':
	$this->setTemplate('xml');
	$this->getResponse()->setContentType('text/xml');
	break;
    default:
      $this->forward404();
      
    }
  }
}

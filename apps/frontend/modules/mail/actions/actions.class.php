<?php

/**
 * mail actions.
 *
 * @package    cpc
 * @subpackage mail
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class mailActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeTest(sfWebRequest $request)
  {
    echo $this->getComponent('mail', 'send', array('action' => $this, 'subject'=>'Un test', 'to'=>array('tangui@localhost'), 'partial'=>'test', 'mailContext'=>array()));
    return sfView::HEADER_ONLY;
  }
}

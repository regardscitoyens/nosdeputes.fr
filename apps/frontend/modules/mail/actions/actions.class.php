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
    $this->getComponent('mail', 'send', 
			     array(
				   'subject'=>'Un test', //Sujet du mail
				   'to'=>array('tangui@localhost'), //Destinataires
				   'partial'=>'test', //Partial contenant le contenu du mail (stocké dans template/_<nom du partial>.php
				   'mailContext'=>array() //Arguments passés au partial
				   ));
    return sfView::HEADER_ONLY;
  }
}

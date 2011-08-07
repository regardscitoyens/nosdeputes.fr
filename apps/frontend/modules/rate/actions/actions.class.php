<?php

/**
 * rate actions.
 *
 * @package    cpc
 * @subpackage rate
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class rateActions extends sfActions
{

  static $rate_conversion = array('0' => 0, '1' => 1, '2' => 2);
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeRateIt(sfWebRequest $request)
  {
    if (!$this->getUser()->isAuthenticated()) {
      $this->getUser()->setFlash('notice', 'Vous devez être identifié pour donner une opinion sur cet élement');
      return $this->redirect($request->getReferer());
    }
    $citoyen_id = $this->getUser()->getAttribute('user_id');
    $note = $request->getParameter('rate');
    $id = $request->getParameter('object_id');
    $type = $request->getParameter('object_type');
    $object = Doctrine::getTable($type)->find($id);
    $this->forward404Unless($object);
    
    $rate = new Rate();
    $rate->rate = self::$rate_conversion[$note];
    $rate->object_type = $object;
    $rate->object_id = $id;
    $rate->citoyen_id = $citoyen_id;
    try {
      $rate->save();
    }catch(Exception $e) {
      $this->getUser()->setFlash('error', 'Vous ne pouvez donner votre avis qu\'une seule fois');
      return $this->redirect($request->getReferer());
    }

    $a = Doctrine_Query::create()
      ->from('Rate')
      ->select('AVG(rate) as rate')
      ->where('object_type = ?', $type)
      ->andWhere('object_id = ?', $id)
      ->fetchArray();

    $object->rate = $a[0]['rate'];
    $object->save();

    $this->getUser()->setFlash('notice', 'Merci ! Votre opinion a bien été prise en compte');
    return $this->redirect($request->getReferer());
  }
}

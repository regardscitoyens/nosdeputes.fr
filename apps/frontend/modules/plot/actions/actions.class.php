<?php

/**
 * plot actions.
 *
 * @package    cpc
 * @subpackage plot
 * @author     roux
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class plotActions extends sfActions {

  public function executeGeneratePlotParlementaire(sfWebRequest $request) {
    $this->type = $request->getParameter('type');
    $this->time = $request->getParameter('time');
    $this->forward404Unless(preg_match('/^(total|hemicycle|commission)$/', $this->type));
    $this->forward404Unless(preg_match('/^(legislature|lastyear|20\d{2}20\d{2})$/', $this->time));
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->questions = $request->getParameter('questions');
    if (!$this->questions)
      $this->questions = 'false';
    $this->link = $request->getParameter('link');
    if (!$this->link)
      $this->link = 'false';
    $this->drawAction = $request->getParameter('drawAction');
    if (!$this->drawAction)
      $this->drawAction = "draw";
    $this->mapId = $request->getParameter('mapId');
    $this->forward404Unless($this->mapId && preg_match('/^Map_.*_\d+\.map$/', $this->mapId));
    sfConfig::set('sf_web_debug', false);
    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $this->getResponse()->addCacheControlHttpHeader('max-age='.(60*60*12).',public');
    $this->getResponse()->setHttpHeader('Expires', $this->getResponse()->getDate(time()+60*60*12));

  }

  public function executeGeneratePlotGroupes(sfWebRequest $request) {
    $this->drawAction = $request->getParameter('drawAction');
    if (!$this->drawAction)
      $this->drawAction = "draw";
    $this->mapId = $request->getParameter('mapId');
    $this->forward404Unless($this->mapId && preg_match('/^Map_\d+\.map$/', $this->mapId));
    $this->type = $request->getParameter('type');
    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $this->getResponse()->addCacheControlHttpHeader('public,max_age='.(60*60*12));
    $this->getResponse()->setHttpHeader('Expires', $this->getResponse()->getDate(time()+60*60*12));
  }
  
}

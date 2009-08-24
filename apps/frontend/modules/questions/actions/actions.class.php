<?php

/**
 * questions actions.
 *
 * @package    cpc
 * @subpackage questions
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class questionsActions extends sfActions
{
  public function executeShow(sfWebRequest $request)
  {
    $this->question = doctrine::getTable('QuestionEcrite')->find($request->getParameter('id'));
    $this->parlementaire = doctrine::getTable('Parlementaire')->find($this->question->parlementaire_id);
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->questions = doctrine::getTable('QuestionEcrite')->createQuery('a')
     ->where('a.parlementaire_id = ?', $this->parlementaire->id)
     ->orderBy('a.updated_at DESC')
     ->execute();
  }
}

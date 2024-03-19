<?php

/**
 * tag actions.
 *
 * @package    cpc
 * @subpackage tag
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class tagActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    $this->session = 0;
    $this->all = 0;
    $this->last = 0;

    $qids = Doctrine::getTable('Intervention')->createQuery('i')
      ->select('i.id')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id);
    if ($request->getParameter('session')) {
      $this->session = $request->getParameter('session');
      $qids->leftJoin('i.Seance s')->andWhere('s.session = ?', $this->session);
    } elseif (!$request->getParameter('all') && !$this->parlementaire->fin_mandat) {
      $this->last = 1;
      $qids->andWhere('i.date > ?', date('Y-m-d', myTools::getEndDataTime()-60*60*24*365));
    } else $this->all = 1;
    $ids = $qids->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t')
      ->where('tg.taggable_model = ?', 'Intervention');
    if (count($ids))
      $this->qtag->andWhereIn('tg.taggable_id', $ids);
    else $this->qtag->andWhere('FALSE');

    $this->sessions = Doctrine_Query::create()
      ->select('s.session')
      ->from("Seance s")
      ->leftJoin('s.Interventions i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('s.session IS NOT NULL AND s.session <> ""')
      ->groupBy('s.session')->fetchArray();
  }
}

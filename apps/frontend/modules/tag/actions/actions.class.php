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

    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t, Intervention i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.id = tg.taggable_id');

    $this->sessions = Doctrine_Query::create()
      ->select('s.session')
      ->from("Seance s")
      ->leftJoin('s.Interventions i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('s.session IS NOT NULL AND s.session <> ""')
      ->groupBy('s.session')->fetchArray();

    $this->session = 0;
    $this->all = 0;
    $this->last = 0;

    if ($request->getParameter('session')) {
      $this->session = $request->getParameter('session');
      $this->qtag->leftJoin('i.Seance s')->andWhere('s.session = ?', $request->getParameter('session'));
    } else if ($request->getParameter('all') || $this->parlementaire->fin_mandat) {
      $this->all = 1;
    } else {
      $this->last = 1;
      $this->qtag->andWhere('i.date > ?', date('Y-m-d', time()-60*60*24*365));
    }
    $this->mois = min(12, floor((time() - strtotime($this->parlementaire->debut_mandat) ) / (60*60*24*30)));
    $this->txtmois = ($this->mois < 2 ? " premier" : "s $this->mois ".($this->mois < 12 ? "prem" : "dern")."iers");
    myTools::setPageTitle('Champ lexical de '.$this->parlementaire->nom.' sur '.($this->last ? "le".$this->txtmois." mois" : ($this->all ? 'tout son mandat' : ($this->session ? 'la session '.preg_replace('/^(\d{4})/', '\\1-', $this->session): ""))), $this->response);
  }
}

<?php

/**
 * intervention actions.
 *
 * @package    cpc
 * @subpackage intervention
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class interventionActions extends sfActions
{
  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->interventions = doctrine::getTable('Intervention')->createQuery('i')->where('i.parlementaire_id = ?', $this->parlementaire->id);
  }
  public function executeShow(sfWebRequest $request)
  {
    $this->intervention = doctrine::getTable('Intervention')->createquery('i')
      ->where('i.id = ?', $request->getParameter('id'))
      ->fetchOne();
    $this->forward404Unless($this->intervention);
  }
  public function executeTop(sfWebRequest $request)
  {
    $q = Doctrine_Query::create()
      ->select('p.*, count(i.id) as nb')
      ->from('Parlementaire p')
      ->leftJoin('p.Interventions i')
      ->groupBy('p.id')
      ->orderBy('nb DESC');
    $this->top = $q->fetchArray();
  }
  public function executeSeance(sfWebRequest $request)
  {
    $seance_id = $request->getParameter('seance');
    $this->seance = doctrine::getTable('Seance')->find($seance_id);
    $this->forward404Unless($this->seance);
    $query = doctrine::getTable('Intervention')->createquery('i')
        ->where('i.seance_id = ?', $seance_id)
        ->orderBy('i.timestamp ASC');
    $qtag = Doctrine_Query::create();
    $qtag->from('Tagging tg, tg.Tag t, Intervention i');
    $qtag->where('i.seance_id = ?', $seance_id);
    $qtag->andWhere('i.id = tg.taggable_id');
    $qtag->andWhere('t.name NOT LIKE ?', 'loi:%');
    $this->tags = PluginTagTable::getPopulars($qtag, array('model' => 'Intervention'));
    $this->interventions = $query->execute();
  }

  public function executeTag(sfWebRequest $request) 
  {
    $this->tags = split('\|', $request->getParameter('tags'));
    
    if (doctrine::getTable('Tag')->findOneByName($this->tags[0]))
      $query = PluginTagTable::getObjectTaggedWithQuery('Intervention', $this->tags);
    else
      $query = doctrine::getTable('Intervention')
	->createQuery('Intervention')->where('0');

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = doctrine::getTable('Parlementaire')
	->findOneBySlug($slug);
      if ($this->parlementaire)
	$query->andWhere('Intervention.parlementaire_id = ?', $this->parlementaire->id);
    }

    if ($section = $request->getParameter('section')) {
      $query->andWhere('(Intervention.section_id = ? OR si.section_id = ?)', array($section, $section))
	->leftJoin('Intervention.Section si');
    }

    $this->query = $query;
  }

public function executeSearch(sfWebRequest $request)
  {
    $this->mots = $request->getParameter('search');
    $mots = $this->mots;
    $mcle = array();

    if (preg_match_all('/("[^"]+")/', $mots, $quotes)) {
      foreach(array_values($quotes[0]) as $q)
	$mcle[] = '+'.$q;
      $mots = preg_replace('/\s*"([^\"]+)"\s*/', ' ', $mots);
    }

    foreach(split(' ', $mots) as $mot) {
      if ($mot && !preg_match('/^[\-\+]/', $mot))
	$mcle[] = '+'.$mot;
    }

    $this->high = array();
    foreach($mcle as $m) {
      $this->high[] = preg_replace('/^[+-]"?([^"]*)"?$/', '\\1', $m);
    }

    $sql = 'SELECT i.id FROM intervention i WHERE MATCH (i.intervention) AGAINST (\''.str_replace("'", "\\'", implode(' ', $mcle)).'\' IN BOOLEAN MODE)';

    $search = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();

    $ids = array();
    foreach($search as $s) {
      $ids[] = $s['id'];
    }

    $this->query = doctrine::getTable('Intervention')->createQuery('i');
    if (count($ids))
      $this->query->whereIn('i.id', $ids);
    else if (count($mcle))
      foreach($mcle as $m)
	$this->query->andWhere('i.intervention LIKE ?', '% '.$m.' %');
    else {
      $this->query->where('0');
      return ;
    }

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = doctrine::getTable('Parlementaire')
	->findOneBySlug($slug);
      if ($this->parlementaire)
	$this->query->andWhere('i.parlementaire_id = ?', $this->parlementaire->id);
    }

    if ($section = $request->getParameter('section')) {
      $this->query->andWhere('(Intervention.section_id = ? OR si.section_id = ?)', array($section, $section))
	->leftJoin('i.Section si');
    }
    $this->query->orderBy('date DESC');
  }
}

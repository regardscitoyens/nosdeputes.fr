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
  }
  public function executeShow(sfWebRequest $request)
  {
    $query = doctrine::getTable('Intervention')->createquery('i')
        ->where('i.id = ?', $request->getParameter('id'))
        ->leftJoin('i.PersonnaliteInterventions pis')
        ->leftJoin('pis.Personnalite pe')
        ->leftJoin('pis.Parlementaire pa');
     $this->intervention = $query->fetchOne();
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
        ->leftJoin('i.PersonnaliteInterventions pis')
        ->leftJoin('pis.Personnalite pe')
        ->leftJoin('pis.Parlementaire pa')
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
	->createQuery('i')->where('0');

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = doctrine::getTable('Parlementaire')
	->findOneBySlug($slug);
      if ($this->parlementaire)
	$query->andWhere('pi.parlementaire_id = ?', $this->parlementaire->id)
	  ->leftJoin('Intervention.PersonnaliteIntervention pi');
    }

    if ($section = $request->getParameter('section')) {
      $query->andWhere('(Intervention.section_id = ? OR si.section_id = ?)', array($section, $section))
	->leftJoin('Intervention.Section si');
    }

    $this->query = $query;
  }
}

<?php

/**
 * section actions.
 *
 * @package    cpc
 * @subpackage section
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class sectionActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
  }

  public function executeParlementaireSection(sfWebRequest $request) 
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    $this->section = doctrine::getTable('Section')->find($request->getParameter('id'));
    $this->forward404Unless($this->section);

    $this->interventions = doctrine::getTable('Intervention')->createQuery('i')
      ->leftJoin('i.PersonnaliteInterventions pi')
      ->where('pi.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('i.Section s')
      ->andWhere('s.section_id = ?', $this->section->id)
      ->andWhere('i.nb_mots > 20')
      ->execute();
  }
  public function executeShow(sfWebRequest $request) 
  {
    $this->section = doctrine::getTable('Section')->find($request->getParameter('id'));
    $this->forward404Unless($this->section);

    $inters = Doctrine_Query::create()
      ->select('i.id')
      ->from('Intervention i')
      ->leftJoin('i.Section s')
      ->where('(i.section_id = ? OR s.section_id = ?)', array($this->section->id, $this->section->id))
      ->andWhere('i.nb_mots > 20')
      ->fetchArray();
    
    foreach($inters as $i) {
      $interventions[] = $i['id'];
    }

    
    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t')
      ->whereIn('tg.taggable_id', $interventions);
    
    
    $this->ptag = doctrine_query::create()
      ->from('Parlementaire p')
      ->leftJoin('p.PersonnaliteIntervention pi')
      ->whereIn('pi.intervention_id', $interventions)
      ->andWhere('(pi.fonction <> ? AND pi.fonction <> ? )', array('président', 'présidente'))
      ;
  }
}

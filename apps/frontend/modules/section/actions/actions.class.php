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

    $this->qinterventions = doctrine::getTable('Intervention')->createQuery('i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('i.Section s')
      ->andWhere('(s.section_id = ? OR s.id = ?)', array($this->section->id, $this->section->id))
      ->andWhere('i.nb_mots > 20')
      ->orderBy('i.date DESC, i.timestamp ASC');
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
    
    $interventions = array();
    foreach($inters as $i) {
      $interventions[] = $i['id'];
    }

    //    $this->forward404Unless(count($interventions));
      
    
    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t')
      ->whereIn('tg.taggable_id', $interventions);
    
    
    $this->ptag = Doctrine_Query::create()
      ->from('Intervention i')
      ->leftJoin('i.Parlementaire p')
      ->where('p.id IS NOT NULL')
      ->whereIn('i.id', $interventions)
      ->andWhere('((i.fonction != ? AND i.fonction != ? ) OR i.fonction IS NULL)', array('président', 'présidente'))
      ->groupBy('p.id')
      ;
  }
  public function executeList(sfWebRequest $request) 
  {

    $this->sections = doctrine::getTable('Section')->createQuery('s')
      ->where('s.id = s.section_id')
      ->orderBy('s.nb_interventions DESC')
      ->execute();

  }
}

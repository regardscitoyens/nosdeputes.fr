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
    $section_id = $request->getParameter('id');
    $this->forward404Unless($section_id);
    $this->section = doctrine::getTable('Section')->find($section_id);
    $this->forward404Unless($this->section);

    $this->lois = $this->section->getTags(array('is_triple' => true,
                                                'namespace' => 'loi',
						                        'key' => 'numero',
                      						    'return'    => 'value'));
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
      ->from('Tagging tg, tg.Tag t');
    if (count($interventions))
      $this->qtag->whereIn('tg.taggable_id', $interventions);
    else
      $this->qtag->where('false');
    
    $this->ptag = Doctrine_Query::create()
      ->from('Intervention i')
      ->leftJoin('i.Parlementaire p')
      ->where('p.id IS NOT NULL')
      ->andWhere('((i.fonction != ? AND i.fonction != ? ) OR i.fonction IS NULL)', array('président', 'présidente'))
      ->groupBy('p.id')
      ;
    if (count($interventions))
      $this->ptag->whereIn('i.id', $interventions);
    else
      $this->ptag->where('false');


  }
  public function executeList(sfWebRequest $request) 
  {
    if (!($order = $request->getParameter('order')))
      $order = 'plus';
    $query = doctrine::getTable('Section')->createQuery('s')
      ->where('s.id = s.section_id')
      ->andWhere('s.nb_interventions > 5');
    if ($order == 'date')
      $query->orderBy('s.min_date DESC');
    else if ($order == 'plus')
      $query->orderBy('s.nb_interventions DESC');
    else forward404();
    $this->sections = $query->execute();

  }
}

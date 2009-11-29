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
    $this->titre = 'Dossiers parlementaires';
    $this->response->setTitle($this->titre.' de '.$this->parlementaire->nom);
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

    $this->lois = $this->section->getTags(array('is_triple' => true,
                                                'namespace' => 'loi',
                                                'key' => 'numero',
                                                'return' => 'value'));
    $amdmts_lois = Doctrine_Query::create()->select('distinct(a.texteloi_id)')->from('Amendement a')->whereIn('a.texteloi_id', $this->lois)->fetchArray();
    $this->lois_amendees = array();
    foreach($amdmts_lois as $loi)
      array_push($this->lois_amendees, $loi['distinct']); 
    sort($this->lois);
    sort($this->lois_amendees);
    
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
    
    $request->setParameter('rss', array(array('link' => '@section_rss_commentaires?id='.$this->section->id, 'title'=>'Les commentaires sur '.$this->section->titre)));
  }

  public function executeList(sfWebRequest $request) 
  {
    if (!($this->order = $request->getParameter('order')))
      $this->order = 'plus';
    $query = doctrine::getTable('Section')->createQuery('s')
      ->where('s.id = s.section_id')
      ->andWhere('s.nb_interventions > 5');
    if ($this->order == 'date') {
      $query->orderBy('s.min_date DESC');
      $this->titre = 'Les derniers dossiers parlementaires';
    } else if ($this->order == 'plus') {
      $query->orderBy('s.nb_interventions DESC');
      $this->titre = 'Les dossiers parlementaires les plus discutés';
    }
    else forward404();
    $this->getResponse()->setTitle($this->titre);
    $this->sections = $query->execute();

  }
}

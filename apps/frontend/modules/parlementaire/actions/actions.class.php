<?php

/**
 * parlementaire actions.
 *
 * @package    cpc
 * @subpackage parlementaire
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class parlementaireActions extends sfActions
{
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
  }

  public function executeList(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->search = $request->getParameter('search');
    if ($this->search) {
      $query->where('p.nom LIKE "%'.$this->search.'%"');
    }
    $query->orderBy("p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
    if ($this->pager->getNbResults() == 1) {
      $p = $this->pager->getResults();
      return $this->redirect('parlementaire/show?slug='.$p[0]->slug);
    }
  }
  public function executeListCirco(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->circo = $request->getParameter('nom_circo');
    if ($this->circo) {
      $query->where('p.nom_circo = ?', $this->circo);
    }
    $query->orderBy("p.num_circo");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
  }
  public function executeListProfession(sfWebRequest $request) 
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->prof = $request->getParameter('profession');
    if ($this->prof) {
      $query->where('p.profession LIKE "%'.$this->prof.'%"');
    }
    $query->orderBy("p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
  }
  public function executeListOrganisme(sfWebRequest $request) 
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $query2 = Doctrine::getTable('Organisme')->createQuery('o');
    $orga = $request->getParameter('slug');
    if ($orga) {
      $query->leftJoin('p.ParlementaireOrganisme po')
        ->leftJoin('po.Organisme o')
        ->where('o.slug = ?', $orga);
      $query2->where('o.slug = ?', $orga);
    }
    $query->orderBy("po.fonction DESC, p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
    $this->orga = $query2->fetchOne();
  }
}

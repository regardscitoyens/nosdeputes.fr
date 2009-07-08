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
    if ($search = $request->getParameter('search')) {
      $query->where("p.nom LIKE '%$search%'");
    }
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
    if ($this->pager->getNbResults() == 1) {
      $p = $this->pager->getResults();
      return $this->redirect('parlementaire/show?slug='.$p[0]->slug);
    }
  }
  public function executeListProfession(sfWebRequest $request) 
  {
    $prof = $request->getParameter('profession');
    $query = Doctrine::getTable('Parlementaire')->createQuery('p')->where('p.profession LIKE "%'.$request->getParameter('profession').'%"')->orderBy('p.nom_de_famille');
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
  }
  public function executeListOrganisme(sfWebRequest $request) 
  {
    $prof = $request->getParameter('organisme');
    $query = Doctrine::getTable('Parlementaire')->createQuery('p')->leftJoin('p.ParlementaireOrganisme po')->leftJoin('po.Organisme o')->where('o.slug = ?', $request->getParameter('slug'));
    $query->orderBy('p.nom_de_famille');
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
  }
}

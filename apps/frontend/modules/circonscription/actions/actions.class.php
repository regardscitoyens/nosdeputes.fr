<?php

/**
 * circonscription actions.
 *
 * @package    cpc
 * @subpackage circonscription
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class circonscriptionActions extends sfActions
{

  public function executeList(sfWebRequest $request) 
  {
    $this->circos = Parlementaire::$dptmt_nom;
  }
  public function executeMap(sfWebRequest $request) 
  {
  }
  public function executeShow(sfWebRequest $request) 
  {
    $this->circo = preg_replace('/_/', ' ', $request->getParameter('departement'));
    $this->forward404Unless($this->circo);
    $this->departement_num = Parlementaire::getNumeroDepartement($this->circo);

    $this->parlementaires = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->where('p.nom_circo = ?', $this->circo)
      ->addOrderBy('p.num_circo')
      ->execute();
    $this->total = count($this->parlementaires);
    $this->forward404Unless($this->total);
    if ($this->total == 1) 
        return $this->redirect('@parlementaire?slug='.$this->parlementaires[0]['slug']); 
  }
  public function executeSearch(sfWebRequest $request) 
  {
    $this->search = $request->getParameter('search');
    $departmt = strip_tags(trim(strtolower($this->search)));
    if (preg_match('/(polyn[eé]sie)/i', $departmt)) {
      $this->num = 987;
      $this->circo = "Polynésie Française";
    } else {
      if (preg_match('/^(\d+\w?)$/', $departmt, $match)) {
	$this->num = preg_replace('/^0+/', '', $match[1]);
      }
      if (! $this->num )
	$this->circo = $departmt;
      else 
	$this->circo = Parlementaire::getNomDepartement($this->num);
      
      $this->query_parlementaires = Doctrine::getTable('Parlementaire')->createQuery('p');
      if ($this->num) {
	$this->query_parlementaires->where('num_circo = ?', $this->num);
      }else{
	$this->query_parlementaires->where('nom_circo LIKE ?', '%'.$this->circo.'%');
      }
      $this->query_parlementaires->addOrderBy('nom_circo, num_circo');
    }
  }
  public function executeRedirect(sfWebRequest $request) 
  {
    $departement = $request->getParameter('departement');
    $num = $request->getParameter('numero');
    $code = $request->getParameter('code');
    if (preg_match('/0*([^0]\d*)\-0*([^0]\d*)/', $code, $match)) {
      $departement = $match[1];
      $num = $match[2];      
    }
    $parlementaire = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->where('num_circo = ?', $num)
      ->andWhere('nom_circo = ?', parlementaire::getNomDepartement($departement))
      ->fetchOne();
    if (!$parlementaire) {
      return $this->redirect('circonscription/list?departement='.$departement);
    }
    return $this->redirect('parlementaire/show?slug='.$parlementaire->slug);
  }
}

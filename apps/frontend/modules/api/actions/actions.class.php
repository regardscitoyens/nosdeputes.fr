<?php

/**
 * api actions.
 *
 * @package    cpc
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class apiActions extends sfActions
{
  public function executeSynthese(sfWebRequest $request)
  {
    
  }
  public function executeDocument(sfWebRequest $request)
  {
    $class = $request->getParameter('class');
    $format = $request->getParameter('format');
    $id = $request->getParameter('id');
    $this->forward404Unless($class);
    $o = doctrine::getTable($class)->find($id);
    if ($class == 'Parlementaire') {
      return $this->redirect('api/parlementaire?slug='.$o->slug.'&format='.$format);
    }
    $slug = $class.'_'.$id;
    $date = $o->updated_at; 
    $this->res = array();
    $this->res[strtolower($class)] = $o->toArray();
    $this->templatize($request, 'nosdeputes.fr_'.'_'.$slug.'_'.$date);
    $this->breakline = '';
  }
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeTop(sfWebRequest $request)
  {
    $date = $request->getParameter('date');
    $this->forward404Unless(preg_match('/(\d{2,4})-?(\d{2})/', $date, $d));
    $date = preg_replace('/-/', '', $date);
    $date = preg_replace('/^(\d{2})(\d{2})$/', '20\\1\\2', $date);
    $d[1] = preg_replace('/^(\d{2})$/', '20\\1', $d[1]);
    $vg = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_month_'.$d[1].'_'.$d[2]);
    $top = unserialize($vg->value);

    $this->forward404Unless($top);

    $this->res = array();
    $this->champs = array();
    foreach(array_keys($top) as $id) {
      $depute['id'] = $id;
      $this->champs['id'] = 1;

      foreach (array_keys($top[$id]) as $k) {
	//Gestion de l'ordre des parametres
	$kfinal = preg_replace('/^\d*_/', '', $k);
	$depute[$kfinal] = $top[$id][$k]['value'];
	$this->champs[$kfinal] = 1;
      }
      $this->res["deputes"][] = array('depute' => $depute);
    }

    for($i = 0 ; $i < count($this->res["deputes"]) ; $i++) {
      foreach(array_keys($this->champs) as $key) {
	if (!isset($this->res['deputes'][$i]['depute'][$key])) {
	  $this->res['deputes'][$i]['depute'][$key] = 0;
	}
      }
    }

    $this->breakline = 'depute';
    $this->templatize($request, $date.'_stats_deputes');
  }

  protected function array2hash($array, $hashname) {
    if (!$array)
      return '';
    $hash = array();
    if (!isset($array[0])) {
      return $array;
    }
    foreach($array as $e) {
      if ($e)
	$hash[] = array($hashname => preg_replace('/\n/', ', ', $e));
    }
    return $hash;
  }

  public function executeListParlementaires(sfWebRequest $request) 
  {
    $deputes = Doctrine::getTable('Parlementaire')->createQuery('p')->execute();
    $this->res = array('deputes' => array());
    $this->champs = array();
    $this->breakline = 'depute';
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    foreach($deputes as $dep) {
      $depute = array();
      $depute['id'] = $dep->id;
      $this->champs['id'] = 1;
      $depute['nom'] = $dep->nom;
      $this->champs['nom'] = 1;
      if ($dep->fin_mandat && $dep->fin_mandat >= $dep->debut_mandat) 
	$depute['ancien_depute'] = 1;
      else if ($request->getParameter('format') == 'csv')
	$depute['ancien_depute'] = 0;
      $this->champs['ancien_depute'] = 1;
      $depute['mandat_debut'] = $dep->debut_mandat;
      $this->champs['mandat_debut'] = 1;
      if ($request->getParameter('format') == 'csv' || $dep->fin_mandat)
	$depute['mandat_fin'] = $dep->fin_mandat;
      $this->champs['mandat_fin'] = 1;
      $depute['api_url'] = 'http://'.$_SERVER['HTTP_HOST'].url_for('api/parlementaire?format='.$request->getParameter('format').'&slug='.$dep->slug);
      $this->champs['api_url'] = 1;
      $this->res['deputes'][] = array('depute' => $depute);
    }
    $this->templatize($request, 'nosdeputes.fr_deputes');
  }
  public function executeParlementaire(sfWebRequest $request) 
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($slug);

    $depute = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->res = array();
    $this->multi = array();
    $this->res['depute'] = array();
    $this->res['depute']['id'] = $depute->id * 1;
    $this->res['depute']['nom'] = $depute->nom;
    $this->res['depute']['nom_de_famille'] = $depute->getNomFamilleCorrect();
    $this->res['depute']['prenom'] = $depute->getPrenom();
    $this->res['depute']['slug'] = $depute->getSlug();
    $this->res['depute']['num_deptmt'] = $depute->getNumDepartement();
    $this->res['depute']['nom_circo'] = $depute->nom_circo;
    $this->res['depute']['num_circo'] = $depute->num_circo * 1;
    $this->res['depute']['mandat_debut'] = $depute->debut_mandat;
    if ($depute->fin_mandat)
      $this->res['depute']['mandat_fin'] = $depute->fin_mandat;
    $groupe = $depute->getGroupe();
    if (is_object($groupe))
      $this->res['depute']['groupe'] = $groupe->__toString();
    $this->res['depute']['groupe_sigle'] = $depute->groupe_acronyme;
    $this->res['depute']['responsabilites'] = $this->array2hash($depute->getResponsabilites(), 'responsabilite');
    $this->res['depute']['responsabilites_extra_parlementaires'] = $this->array2hash($depute->getExtras(), 'responsabilite');
    $this->multi['responsabilite'] = 1;
    $this->res['depute']['site_web'] = $depute->site_web;
    $this->res['depute']['url_an'] = $depute->url_an;
    $this->res['depute']['emails'] = $this->array2hash(unserialize($depute->mails), 'email');
    $this->multi['email'] = 1;
    $this->res['depute']['adresses'] = $this->array2hash(unserialize($depute->adresses), 'adresse');
    $this->multi['adresse'] = 1;
    $this->res['depute']['autres_mandats'] = $this->array2hash(unserialize($depute->autres_mandats), 'mandat');
    $this->multi['mandat'] = 1;
    $this->res['depute']['profession'] = $depute->profession;
    $this->res['depute']['place_en_hemicycle'] = $depute->place_hemicycle;
    $this->res['depute']['sexe'] = $depute->sexe;
    $this->champ = 'depute';
    $this->breakline = '';
    $date = $depute->updated_at.'';
    $date = preg_replace('/[- :]/', '', $date);
    $this->templatize($request, 'nosdeputes.fr_'.'_'.$slug.'_'.$date);
  }

  private function templatize($request, $filename) {
    $this->setLayout(false);
    switch($request->getParameter('format')) {
      case 'json':
	$this->setTemplate('json');
	if (!$request->getParameter('textplain')) {
	  $this->getResponse()->setContentType('text/plain; charset=utf-8');
	  $this->getResponse()->setHttpHeader('content-disposition', 'attachment; filename="'.$filename.'.json"');
	}
	break;
      case 'xml':
	$this->setTemplate('xml');
	if (!$request->getParameter('textplain')) {
	  $this->getResponse()->setContentType('text/xml; charset=utf-8');
	  //	$this->getResponse()->setHttpHeader('content-disposition', 'attachment; filename="'.$filename.'.xml"');
	}
	break;
      case 'csv':
	$this->setTemplate('csv');
	if (!$request->getParameter('textplain')) {
	  $this->getResponse()->setContentType('application/csv; charset=utf-8');
	  $this->getResponse()->setHttpHeader('content-disposition', 'attachment; filename="'.$filename.'.csv"');
	}
	break;
    default:
      $this->forward404();
    }
    if ($request->getParameter('textplain')) {
      $this->getResponse()->setContentType('text/plain; charset=utf-8');
    }
  }
}

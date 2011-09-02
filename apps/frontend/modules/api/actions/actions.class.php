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
    $this->templatize($request, 'nossenateurs.fr_'.'_'.$slug.'_'.$date);
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
    $this->forward404Unless(preg_match('/(\d{2,4})-?\d{2}/', $date, $d));
    $date = preg_replace('/-/', '', $date);
    $date = preg_replace('/^(\d{2})(\d{2})$/', '20\\1\\2', $date);
    $d[1] = preg_replace('/^(\d{2})$/', '20\\1', $d[1]);
    $vg = Doctrine::getTable('VariableGlobale')->findOneByChamp('stats_month_'.$d[1]);
    $top = unserialize($vg->value);

    $this->forward404Unless(isset($top[$date]));

    $this->res = array();
    $this->champs = array();
    foreach(array_keys($top[$date]) as $id) {
      $senateur['id'] = $id;
      $this->champs['id'] = 1;

      foreach (array_keys($top[$date][$id]) as $k) {
	//Gestion de l'ordre des parametres
	$kfinal = preg_replace('/^\d*_/', '', $k);
	$senateur[$kfinal] = $top[$date][$id][$k]['value'];
	$this->champs[$kfinal] = 1;
      }
      $this->res["senateurs"][] = array('senateur' => $senateur);
    }

    for($i = 0 ; $i < count($this->res["senateurs"]) ; $i++) {
      foreach(array_keys($this->champs) as $key) {
	if (!isset($this->res['senateurs'][$i]['senateur'][$key])) {
	  $this->res['senateurs'][$i]['senateur'][$key] = 0;
	}
      }
    }

    $this->breakline = 'senateur';
    $this->templatize($request, $date.'_stats_senateurs');
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
    $senateurs = Doctrine::getTable('Parlementaire')->createQuery('p')->execute();
    $this->res = array('senateurs' => array());
    $this->champs = array();
    $this->breakline = 'senateur';
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    foreach($senateurs as $dep) {
      $senateur = array();
      $senateur['id'] = $dep->id;
      $this->champs['id'] = 1;
      $senateur['nom'] = $dep->nom;
      $this->champs['nom'] = 1;
      if ($dep->fin_mandat) 
	$senateur['ancien_senateur'] = 1;
      else if ($request->getParameter('format') == 'csv')
	$senateur['ancien_senateur'] = 0;
      $this->champs['ancien_senateur'] = 1;
      $senateur['mandat_debut'] = $dep->debut_mandat;
      $this->champs['mandat_debut'] = 1;
      if ($request->getParameter('format') == 'csv' || $dep->fin_mandat)
	$senateur['mandat_fin'] = $dep->fin_mandat;
      $this->champs['mandat_fin'] = 1;
      $senateur['api_url'] = 'http://'.$_SERVER['HTTP_HOST'].url_for('api/parlementaire?format='.$request->getParameter('format').'&slug='.$dep->slug);
      $this->champs['api_url'] = 1;
      $this->res['senateurs'][] = array('senateur' => $senateur);
    }
    $this->templatize($request, 'nossenateurs.fr_senateurs');
  }
  public function executeParlementaire(sfWebRequest $request) 
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($slug);

    $senateur = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->res = array();
    $this->multi = array();
    $this->res['senateur'] = array();
    $this->res['senateur']['id'] = $senateur->id * 1;
    $this->res['senateur']['nom'] = $senateur->nom;
    $this->res['senateur']['nom_de_famille'] = $senateur->nom_de_famille;
    $this->res['senateur']['departement'] = $senateur->nom_circo;
    $this->res['senateur']['mandat_debut'] = $senateur->debut_mandat;
    if ($senateur->fin_mandat)
      $this->res['senateur']['mandat_fin'] = $senateur->fin_mandat;
    $this->res['senateur']['groupe'] = $senateur->getGroupe();
    $this->res['senateur']['groupe_sigle'] = $senateur->groupe_acronyme;
    $this->res['senateur']['responsabilites'] = $this->array2hash($senateur->getResponsabilites(), 'responsabilite');
    $this->res['senateur']['responsabilites_extra_parlementaires'] = $this->array2hash($senateur->getExtras(), 'responsabilite');
    $this->multi['responsabilite'] = 1;
    $this->res['senateur']['sites_web'] = $this->array2hash(unserialize($senateur->sites_web), 'sites_web');
    $this->res['senateur']['url_senat'] = $senateur->url_senat;
    $this->res['senateur']['emails'] = $this->array2hash(unserialize($senateur->mails), 'email');
    $this->multi['email'] = 1;
    $this->res['senateur']['adresses'] = $this->array2hash(unserialize($senateur->adresses), 'adresse');
    $this->multi['adresse'] = 1;
    $this->res['senateur']['autres_mandats'] = $this->array2hash(unserialize($senateur->autres_mandats), 'mandat');
    $this->multi['mandat'] = 1;
    $this->res['senateur']['profession'] = $senateur->profession;
    $this->res['senateur']['place_en_hemicycle'] = $senateur->place_hemicycle;
    $this->res['senateur']['sexe'] = $senateur->sexe;
    $this->champ = 'senateur';
    $this->breakline = '';
    $date = $senateur->updated_at.'';
    $date = preg_replace('/[- :]/', '', $date);
    $this->templatize($request, 'nossenateurs.fr_'.'_'.$slug.'_'.$date);
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

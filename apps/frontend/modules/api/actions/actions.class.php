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
      $senateur['id'] = $id;
      $this->champs['id'] = 1;

      foreach (array_keys($top[$id]) as $k) {
	//Gestion de l'ordre des parametres
	$kfinal = preg_replace('/^\d*_/', '', $k);
	$senateur[$kfinal] = $top[$id][$k]['value'];
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

  protected static function array2hash($array, $hashname) {
    if (!$array)
      return '';
    $hash = array();
    if (!isset($array[0])) {
      if (isset($array->fonction))
        return array("organisme" => $array->getNom(), "fonction" => $array->fonction);
      else return $array;
    }
    foreach($array as $e) if ($e) {
      if (isset($e->fonction))
        $hash[] = array($hashname => array("organisme" => $e->getNom(), "fonction" => $e->fonction));
      else $hash[] = array($hashname => preg_replace('/\n/', ', ', $e));
    }
    return $hash;
  }

  public function executeListParlementaires(sfWebRequest $request) 
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    if ($request->getParameter('current') == true) {
      $query->where('fin_mandat IS NULL OR debut_mandat > fin_mandat');
      $this->multi = array();
      $this->multi['responsabilite'] = 1;
      $this->multi['email'] = 1;
      $this->multi['adresse'] = 1;
      $this->multi['mandat'] = 1;
      $this->multi['site'] = 1;
    }
    $senateurs = $query->execute();
    $this->champs = array();
    $this->res = array('senateurs' => array());
    $this->breakline = 'senateur';
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    foreach($senateurs as $dep) {
      if ($request->getParameter('current') == true) {
        $senateur = $this->getParlementaireArray($dep);
        if ($request->getParameter('format') == 'csv')
         foreach(array_keys($senateur) as $key)
          if (!isset($this->champs[$key]))
           $this->champs[$key] = 1;
      } else {
        $senateur = array();
        $senateur['id'] = $dep->id;
        $senateur['nom'] = $dep->nom;
        if ($dep->fin_mandat && $dep->fin_mandat >= $dep->debut_mandat) 
	  $senateur['ancien_senateur'] = 1;
        else if ($request->getParameter('format') == 'csv')
	  $senateur['ancien_senateur'] = 0;
        $senateur['mandat_debut'] = $dep->debut_mandat;
        if ($request->getParameter('format') == 'csv' || $dep->fin_mandat)
          $senateur['mandat_fin'] = $dep->fin_mandat;
        $this->champs['id'] = 1;
        $this->champs['nom'] = 1;
        $this->champs['ancien_senateur'] = 1;
        $this->champs['mandat_debut'] = 1;
        $this->champs['mandat_fin'] = 1;
      }
      $senateur['api_url'] = 'http://'.$_SERVER['HTTP_HOST'].url_for('api/parlementaire?format='.$request->getParameter('format').'&slug='.$dep->slug);
      $this->champs['api_url'] = 1;
      $this->res['senateurs'][] = array('senateur' => $senateur);
    }
    $this->templatize($request, 'nossenateurs.fr_senateurs'.($request->getParameter('current') == true ? "_en_mandat" : ""));
  }

  public function executeParlementaire(sfWebRequest $request) 
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($slug);
    $senateur = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->res = array();
    $this->res['senateur'] = $this->getParlementaireArray($senateur);
    $this->multi = array();
    $this->multi['responsabilite'] = 1;
    $this->multi['email'] = 1;
    $this->multi['adresse'] = 1;
    $this->multi['mandat'] = 1;
    $this->multi['site'] = 1;
    $this->champ = 'senateur';
    $this->breakline = '';
    $date = $senateur->updated_at.'';
    $date = preg_replace('/[- :]/', '', $date);
    $this->templatize($request, 'nossenateurs.fr_'.'_'.$slug.'_'.$date);
  }


  public static function getParlementaireArray($parl) {
    $res = array();
    $res['id'] = $parl->id * 1;
    $res['nom'] = $parl->nom;
    $res['nom_de_famille'] = $parl->nom_de_famille;
    $res['prenom'] = $parl->getPrenom();
    $res['sexe'] = $parl->sexe;
    $res['date_naissance'] = $parl->date_naissance;
    $res['nom_circo'] = $parl->nom_circo;
    $res['num_deptmt'] = $parl->getNumDepartement();
    $res['mandat_debut'] = $parl->debut_mandat;
    if ($parl->fin_mandat)
      $res['mandat_fin'] = $parl->fin_mandat;
    $groupe = $parl->getGroupe();
    if (is_object($groupe))
      $res['groupe'] = self::array2hash($groupe, 'groupe_politique');
    $res['groupe_sigle'] = $parl->groupe_acronyme;
    $res['responsabilites'] = self::array2hash($parl->getResponsabilites(), 'responsabilite');
    $res['responsabilites_extra_parlementaires'] = self::array2hash($parl->getExtras(), 'responsabilite');
    $res['sites_web'] = self::array2hash(unserialize($parl->sites_web), 'site');
    $res['url_institution'] = $parl->url_institution;
    $res['emails'] = self::array2hash(unserialize($parl->mails), 'email');
    $res['adresses'] = self::array2hash(unserialize($parl->adresses), 'adresse');
    $res['autres_mandats'] = self::array2hash(unserialize($parl->autres_mandats), 'mandat');
    $res['profession'] = $parl->profession;
    $res['place_en_hemicycle'] = $parl->place_hemicycle;
    $res['slug'] = $parl->getSlug();
    return $res;
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

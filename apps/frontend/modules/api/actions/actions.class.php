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

  public function executeTopSynthese(sfWebRequest $request) {
    $format = $request->getParameter('format');
    $this->withBOM = $request->getParameter('withBOM');
    $qp = Doctrine::getTable('Parlementaire')->createQuery('p');
    $qp->andWhere('fin_mandat IS NULL');
    $dixmois = time() - round(60*60*24*3650/12);
    $qp->orderBy('nom_de_famille');
    $parlementaires = $qp->execute();
    unset($qp);
    $this->res = array();
    $this->champs = array();
    foreach($parlementaires as $p) {
      $tops = $p->top;
      $parlementaire['id'] = $p->id;
      $this->champs['id'] = 1;
      $parlementaire = $this->getParlementaireArray($p, $format, 2);
      if ($format == 'csv')
       foreach(array_keys($parlementaire) as $key)
        if (!isset($this->champs[$key]))
         $this->champs[$key] = 1;
      foreach(array_keys($tops) as $k) {
        if ($k != 'nb_mois') {
          //Gestion de l'ordre des parametres
          $kfinal = preg_replace('/^\d*_/', '', $k);
          $parlementaire[$kfinal] = $tops[$k]['value'];
          if (!isset($this->champs[$kfinal])) $this->champs[$kfinal] = 1;
        } else {
          $parlementaire[$k] = $tops[$k];
          if (!isset($this->champs[$k])) $this->champs[$k] = 1;
        }
      }
      $this->res["parlementaires"][] = array('parlementaire' => $parlementaire);
    }

    for($i = 0 ; $i < count($this->res["parlementaires"]) ; $i++) {
      foreach(array_keys($this->champs) as $key) {
        if (!isset($this->res['parlementaires'][$i]['parlementaire'][$key])) {
          $this->res['parlementaires'][$i]['parlementaire'][$key] = 0;
        }
      }
    }

    $this->breakline = 'parlementaire';
    $this->templatize($request, 'nosenateurs.fr_synthese_'.date('Y-m-d'));
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
      $senateur = $this->getParlementaireArray($dep, $request->getParameter('format'), ($request->getParameter('current') == true ? 1 : 2));
      if ($request->getParameter('format') == 'csv')
       foreach(array_keys($senateur) as $key)
        if (!isset($this->champs[$key]))
         $this->champs[$key] = 1;
      $this->res['senateurs'][] = array('senateur' => $senateur);
    }
    $this->templatize($request, 'nossenateurs.fr_senateurs'.($request->getParameter('current') == true ? "_en_mandat" : ""));
  }

  public function executeParlementaire(sfWebRequest $request) 
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($slug);
    $senateur = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    if (!$senateur) {
        $senateur = Doctrine::getTable('Parlementaire')->findOneByNomSexeGroupeCirco($slug);
        if ($senateur)
                return $this->redirect('api/parlementaire?slug='.$senateur->slug.'&format='.$request->getParameter('format'));
    }

    $this->forward404Unless($senateur);
    $this->res = array();
    $this->res['senateur'] = $this->getParlementaireArray($senateur, $request->getParameter('format'));
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


  public static function getParlementaireArray($parl, $format, $light = 0) {
    $res = array();
    if (!$parl)
        throw new Exception("pas de parlementaire");
    $res['id'] = $parl->id * 1;
    $res['nom'] = $parl->nom;
    $res['nom_de_famille'] = $parl->nom_de_famille;
    $res['prenom'] = $parl->getPrenom();
    $res['sexe'] = $parl->sexe;
    $res['date_naissance'] = $parl->date_naissance;
    $res['nom_circo'] = $parl->nom_circo;
    $res['num_deptmt'] = $parl->getNumDepartement();
    //Pour conserver la cohérence des CSV entre ND et NS, on ajoute deux champs vides :
    $res['champs_specifique_deputes_1'] = '';
    $res['champs_specifique_deputes_2'] = '';
    $res['mandat_debut'] = $parl->debut_mandat;
    if ($parl->fin_mandat)
      $res['mandat_fin'] = $parl->fin_mandat;
    else if ($format == 'csv' && $light != 1)
      $res['mandat_fin'] = "";
    if ($parl->fin_mandat && $parl->fin_mandat >= $parl->debut_mandat)
      $res['ancien_senateur'] = 1;
    else if ($format == 'csv' && $light != 1)
      $res['ancien_senateur'] = 0;
    if (!$light) {
      $groupe = $parl->getGroupe();
      if (is_object($groupe))
        $res['groupe'] = self::array2hash($groupe, 'groupe_politique');
      else if ($format == 'csv')
        $res['groupe'] = "";
    }
    $res['groupe_sigle'] = $parl->groupe_acronyme;
    if (!$light) {
      $res['responsabilites'] = self::array2hash($parl->getResponsabilites(), 'responsabilite');
      $res['responsabilites_extra_parlementaires'] = self::array2hash($parl->getExtras(), 'responsabilite');
      $res['groupes_parlementaires'] = self::array2hash($parl->getGroupes(), 'responsabilite');
    }
    if ($light != 2) {
      $res['sites_web'] = self::array2hash(unserialize($parl->sites_web), 'site');
      $res['emails'] = self::array2hash(unserialize($parl->mails), 'email');
      $res['adresses'] = self::array2hash(unserialize($parl->adresses), 'adresse');
      $res['anciens_mandats'] = self::array2hash(unserialize($parl->anciens_mandats), 'mandat');
      $res['autres_mandats'] = self::array2hash(unserialize($parl->autres_mandats), 'mandat');
    }
    $res['profession'] = $parl->profession;
    $res['place_en_hemicycle'] = $parl->place_hemicycle;
    $res['url_institution'] = $parl->url_institution;
    $res['id_institution'] = $parl->id_institution;
    $res['slug'] = $parl->getSlug();
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    $res['url_nossenateurs'] = url_for('@parlementaire?slug='.$res['slug'], 'absolute=true');
    $res['url_nossenateurs_api'] = url_for('api/parlementaire?format='.$format.'&slug='.$res['slug'], 'absolute=true');
    $res['nb_mandats'] = count(unserialize($parl->getAutresMandats()));
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

<?php

/**
 * solr actions.
 *
 * @package    cpc
 * @subpackage solr
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class solrActions extends sfActions
{

  private function getPhoto($obj) {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    switch(get_class($obj)) {
    case 'Intervention':
      if ($obj->getParlementaire()->__toString()) {
        return $this->getPartial('parlementaire/photoParlementaire', array('parlementaire'=>$obj->getParlementaire(), 'height'=>70));
      }
    case 'QuestionEcrite':
      return $this->getPartial('parlementaire/photoParlementaire', array('parlementaire'=>$obj->getParlementaire(), 'height'=>70));
    case 'Amendement':
      return '';
    case 'Parlementaire':
      return $this->getPartial('parlementaire/photoParlementaire', array('parlementaire'=>$obj, 'height'=>70));
    case 'Commentaire':
      return '<img style="width:53px;" class="jstitle" title="'.$obj->getCitoyen()->getLogin().'" alt="'.$obj->getCitoyen()->getLogin().'" src="'.url_for('@photo_citoyen?slug='.$obj->getCitoyen()->getSlug()).'"/>';
    case 'Citoyen':
      return '<img style="width:53px;" class="jstitle" title="'.$obj->getLogin().'" alt="'.$obj->getLogin().'" src="'.url_for('@photo_citoyen?slug='.$obj->getSlug()).'"/>';
    case 'NonObjectPage':
      return $obj->getImage();
    }
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeSearch(sfWebRequest $request)
  {
    if ($search = $request->getParameter('search')) {
      if ($ob = $request->getParameter('object_name'))
        $ob = '&object_name='.$ob;
      return $this->redirect('@recherche_solr?query='.$search.$ob);
    } else if ($request->hasParameter('search'))
      return $this->redirect('@recherche_home?sort=1');

    $this->query = $request->getParameter('query');
    $this->query = preg_replace('#^https?://#', '', $this->query);
    $query = preg_replace('/\*/', '', $this->query);

    myTools::setPageTitle($this->query ? "Résultats de recherche sur \"$query\"" : "Rechercher au travers des travaux parlementaires de l'Assemblée nationale", $this->response);

    $nb = 20;
    $deb = ($request->getParameter('page', 1) - 1) * $nb ;
    $fq = '';
    $this->facet = array();

    $this->selected = array();
    if ($on = $request->getParameter('object_name')) {
      $this->selected['object_name'][$on] = 1;
      $fq .= " object_name:$on";
    }
    if ($tags = $request->getParameter('tag')) {
      foreach(explode(',', $tags) as $tag) {
        if (!$on && preg_match('/^(type|sort)=/', $tag))
          continue;
        $this->selected['tag'][$tag] = 1;
        $fq .= ' tag:"'.$tag.'"';
      }
    }

    if (!strlen($fq)) {
      $fq = '*';
    }

    //Récupère les résultats auprès de SolR
    $date_debut = preg_replace("/-..$/", "-01T00:00:00Z", myTools::getDebutLegislature());
    $date_fin = 'NOW';
    if (myTools::isLegislatureCloturee()) {
        $date_fin = preg_replace("/-..$/", "-01T00:00:00Z", myTools::getFinLegislature());
    }
    $params = array('hl'=>'true', 'fl' => 'id,object_id,object_name,date,description', 'hl.fragsize'=>500, "facet"=>"true", "facet.field"=>array("object_name","tag"), "facet.date" => "date", "facet.date.start"=>$date_debut, "facet.date.end"=>$date_fin, "facet.date.gap"=>"+1MONTH", 'fq' => $fq, "facet.date.include" => "edge", "facet.limit" => 2000);
    $this->sort_type = 'pertinence';

    if (!$this->query) {
	$params['hl'] = 'false';
        $query = "*";
    }

    $this->sort = $request->getParameter('sort');
    $this->ajax = $request->getParameter('ajax');
    $date = $request->getParameter('date');
    $format = $request->getParameter('format');

    $this->tags = 0;
    if ($format) {
      sfConfig::set('sf_web_debug', false);
      $this->tags = $request->getParameter('tags');
      $this->format = $format;
    }

    $this->title = $request->getParameter('title');
    $vue_actuelle = $request->getParameter('mois');

    if ($format == 'rss') {
      $ob = $request->getParameter('object_name');
      $deputenom = $request->getParameter('deputenom');
      if (preg_match('/commentaire/i', $ob) && $deputenom)
        $this->rsstitle = "Les derniers commentaires sur l'activité parlementaire de ".$deputenom;
      $this->setTemplate('rss');
      $this->feed = new sfRssFeed();
      $this->feed->setLanguage('fr');
      $this->sort = 1;
      $date = null;
      $from = null;
    }

    $this->parlfacet = $request->getParameter('parlfacet', 0);
    $this->tagsfacet = $request->getParameter('tagsfacet', 0);
    $this->timefacet = $request->getParameter('timefacet', 0);
    if ($format == 'json') {
      $this->getResponse()->setContentType('text/plain; charset=utf-8');
      $this->setTemplate('json');
      $this->setLayout(false);
    }

    if ($format == 'xml') {
      $this->getResponse()->setContentType('text/xml; charset=utf-8');
      $this->setTemplate('xml');
      $this->setLayout(false);
    }

    if ($format == 'csv') {
      // $this->getResponse()->setContentType('application/csv; charset=utf-8');
      $this->getResponse()->setContentType('text/plain; charset=utf-8');
      $this->setTemplate('csv');
      $this->setLayout(false);
    }

    if ($this->sort) {
      $this->selected['sort'] = 1;
      $params['sort'] = "date desc";
      $this->sort_type = 'date';
    }

    $this->vue = 'par_mois';

    $period = '';

    $jours_max = 90; // Seuil en nb de jours qui détermine l'affichage par jour ou par mois d'une période
    if ($date) {
      $this->selected['date'][$date] = $date;
      if (preg_match('/\d{8}/',$date)) {
        $date = preg_replace('/(\d{4})(\d{2})(\d{2})/', '\1-\2-\3T00:00:00Z', $date);
      }
      $dates = explode(',', $date);
      list($from, $to) = $dates;

      $nbjours = round((strtotime($to) - strtotime($from))/(60*60*24)+1);

      $comp_date_from = explode("T", $from);
      $comp_date_from = explode("-", $comp_date_from[0]);
      $comp_date_from = mktime(0, 0, 0, $comp_date_from[1] + 1, $comp_date_from[2], $comp_date_from[0]);
      $comp_date_from = date("Y-m-d", $comp_date_from).'T00:00:00Z';

      // Affichage d'une période
      if(($nbjours < $jours_max) and ($from != $to) and ($comp_date_from != $to)) {
        $period = 'DAY';
        $this->vue = 'par_jour';
      }
      if($nbjours >= $jours_max || $vue_actuelle) {
        $period = 'MONTH';
        $to = $to.'+1MONTH';
        $this->vue = 'par_mois';
      }
      // Affichage d'un jour
      else if($from == $to) {
        $period = 'DAY';
        $this->vue = 'jour';
      }
      // Affichage d'un mois
      if($comp_date_from == $to) {
        $period = 'DAY';
        $this->vue = 'mois';
      }

      if ($period == 'DAY') {
        $from = date ('Y-m-d', strtotime($from)-(3600*2+1)).'T23:59:59Z';
        $to = date ('Y-m-d', strtotime($to)).'T23:59:59Z';
      }
      $query .= ' date:['.$from.' TO '.$to.']';
      $params['facet.date.start'] = $from;
      $params['facet.date.end'] = $to;
      $params['facet.date.gap'] = '+1'.$period;
    } else if (time() - strtotime($date_debut) < $jours_max*60*60*24) {
      $period = 'DAY';
      $this->vue = 'par_jour';
      $params['facet.date.start'] = date ('Y-m-d', strtotime($date_debut)-(3600*2+1)).'T23:59:59Z';;
      $params['facet.date.end'] = date ('Y-m-d', time()).'T23:59:59Z';
      $params['facet.date.gap'] = '+1'.$period;
    }

    $this->start = $params['facet.date.start'];
    if ($period == 'DAY') {
      $this->start = date ('Ymd', strtotime($this->start)+1);
    }
    $this->end = $params['facet.date.end'];
    if($this->end == 'NOW') {
      $this->end = date("Ymd");
    }


    try {
      $s = new SolrConnector();
      $results = $s->search($query, $params, $deb, $nb);
    }
    catch(Exception $e) {
      $results = array('response' => array('docs' => array(), 'numFound' => 0));
      $this->getUser()->setFlash('error', 'Désolé, le moteur de recherche est indisponible pour le moment. <!-- '.$query." $e".' -->');
    }

    if  (!$format && count($results['response']['docs']) == 1 && $results['response']['docs'][0]['object_name'] == 'Parlementaire' && !$request->getParameter('format') && !$request->getParameter('noredirect')) {
      return $this->redirect($results['response']['docs'][0]['object']->getLink());
    }

    //Reconstitue les résultats
    $this->results = $results['response'];
    for($i = 0 ; $i < count($this->results['docs']) ; $i++) {
      $res = $this->results['docs'][$i];
      $obj = $res['object'];
      $objclass = get_class($obj);
      $this->results['docs'][$i]['link'] = $obj->getLink();
      $this->results['docs'][$i]['photo'] = $this->getPhoto($obj);
      $this->results['docs'][$i]['titre'] = $obj->getTitre();
      if ($objclass === 'Section')
        $this->results['docs'][$i]['titre'] = "Dossier : ".$this->results['docs'][$i]['titre'];
      else if ($objclass === 'Commentaire')
        $this->results['docs'][$i]['titre'] = "Commentaire : ".ucfirst(preg_replace('/^./', strtolower($this->results['docs'][$i]['titre']{0}), $this->results['docs'][$i]['titre']));
      $this->results['docs'][$i]['personne'] = $obj->getPersonne();
      if ($this->query && isset($results['highlighting'][$res['id']]['text'])) {
        $high_res = array();
        foreach($results['highlighting'][$res['id']]['text'] as $h) {
          $h = preg_replace('/.*=/', '', $h);
          array_push($high_res, $h);
        }
        $cleanpersonne = preg_replace("/, .*$/", "", $this->results['docs'][$i]['personne']);
        $this->results['docs'][$i]['highlighting'] = preg_replace('/^'.$cleanpersonne.' /', '', implode('...', $high_res));
      }
      else if (isset($this->results['docs'][$i]['description'])) {
	$this->results['docs'][$i]['highlighting'] = $this->results['docs'][$i]['description'];
      	if (strlen($this->results['docs'][$i]['highlighting']) > 700)
    	   $this->results['docs'][$i]['highlighting'] = preg_replace('/[^ ]*$/', '', substr($this->results['docs'][$i]['description'], 0, 700)).'...';
      } else $this->results['docs'][$i]['highlighting'] = "";
    }

    $this->results['end'] = $deb + $nb;
    $this->results['page'] = $deb/$nb + 1;
    if ($this->results['end'] > $this->results['numFound'] && $this->results['numFound']) {
      $this->results['end'] = $this->results['numFound'] + 1;
    }

    if (isset($results['facet_counts'])) {
      $this->facet['type']['prefix'] = '';
      $this->facet['type']['facet_field'] = 'object_name';
      $this->facet['type']['name'] = 'Types';
      $this->facet['type']['values'] = $results['facet_counts']['facet_fields']['object_name'];

      //Prépare les facets pour les types d'interventions
      if ($on == 'Intervention') {
        $this->facet['intervtypes']['prefix'] = 'type=';
        $this->facet['intervtypes']['facet_field'] = 'tag';
        $this->facet['intervtypes']['name'] = 'Types interventions';
      }

      //Prépare les facets pour les types d'organismes
      if ($on == 'Organisme') {
        $this->facet['orgtypes']['prefix'] = 'type=';
        $this->facet['orgtypes']['facet_field'] = 'tag';
        $this->facet['orgtypes']['name'] = 'Types organismes';
      }

      //Prépare les facets des sorts pour les amendements
      if ($on == 'Amendement') {
        $this->facet['sorts']['prefix'] = 'sort=';
        $this->facet['sorts']['facet_field'] = 'tag';
        $this->facet['sorts']['name'] = 'Sorts';
      }

      //Prépare les facets pour les types de documents
      if ($on == 'Texteloi') {
        $this->facet['documenttypes']['prefix'] = 'type=';
        $this->facet['documenttypes']['facet_field'] = 'tag';
        $this->facet['documenttypes']['name'] = 'Types documents';
      }

      //Prépare les facets des parlementaires
      $this->facet['parlementaires']['prefix'] = 'parlementaire=';
      $this->facet['parlementaires']['facet_field'] = 'tag';
      $this->facet['parlementaires']['name'] = 'Parlementaires';


      //Prépare les facets des mots-clés
      $this->facet['tag']['prefix'] = '';
      $this->facet['tag']['facet_field'] = 'tag';
      $this->facet['tag']['name'] = 'Tags';

      foreach($results['facet_counts']['facet_fields']['tag'] as $tag => $nb ) {
        if (!$nb)
        continue;
        if (!preg_match('/=/', $tag))
          $this->facet['tag']['values'][$tag] = $nb;
        else if (preg_match('/^parlementaire=(.*)/', $tag, $matches))
          $this->facet['parlementaires']['values'][$matches[1]] = $nb;
        else if ($on == 'Amendement' && preg_match('/^sort=(.*)/', $tag, $matches))
          $this->facet['sorts']['values'][$matches[1]] = $nb;
        else if ($on == 'Texteloi' && preg_match('/^type=(.*)/', $tag, $matches))
          $this->facet['documenttypes']['values'][$matches[1]] = $nb;
        else if ($on == 'Intervention' && preg_match('/^type=(.*)/', $tag, $matches))
          $this->facet['intervtypes']['values'][$matches[1]] = $nb;
        else if ($on == 'Organisme' && preg_match('/^type=(.*)/', $tag, $matches))
          $this->facet['orgtypes']['values'][$matches[1]] = $nb;
      }
    }

    if (!$results['response']['numFound']) {
      if ($format)
      return ;
      return $this->setTemplate('noresults');
    }
    $this->fdates = array();
    $this->fdates['max'] = 1;
    foreach($results['facet_counts']['facet_dates']['date'] as $date => $nb) {
      if ($period == 'DAY') {
	$date = date ('Ymd', strtotime($date)+1);
      }else{
	$date = date ('Ymd', strtotime($date));
      }
      if (preg_match('/^20/', $date)) {
        $pc = $nb/$results['response']['numFound'];
        $this->fdates['values'][$date] = array('nb' => $nb, 'pc' => $pc);
        if ($this->fdates['max'] < $pc) {
          $this->fdates['max'] = $pc;
        }
      }
    }
  }

  public function executeRedirect(sfWebRequest $request)
  {
    if ($p = $request->getParameter('slug')) {
      $parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($p);
      if ($parlementaire) {
         $request->setParameter('tag', 'parlementaire='.$parlementaire);
         $request->setParameter('deputenom', $parlementaire->getNom());
      }
    }

    return $this->forward('solr', 'search');
  }
}

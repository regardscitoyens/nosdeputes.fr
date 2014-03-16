<?php

/**
 * intervention actions.
 *
 * @package    cpc
 * @subpackage intervention
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class interventionActions extends sfActions
{
  public function executeParlementaire(sfWebRequest $request)
  {
    if (!$this->type = $request->getParameter('type')) $this->type = 'all';
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');
    $this->interventions = Doctrine::getTable('Intervention')->createQuery('i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id);
    if (preg_match('/(commission|question|loi)$/', $this->type)) {
      $this->interventions->andWhere('i.type = ?', $this->type);
      if ($this->type == 'question')
        $this->interventions->andWhere('i.fonction NOT LIKE ?', 'président%')
          ->andWhere('i.nb_mots > ?', 40)
          ->groupBy('i.seance_id, i.section_id');
    } else if ($this->type != 'all')
      $this->forward404();
    if ($this->type == 'question') $this->titre = 'Questions orales';
    else {
      $this->titre = 'Interventions';
      if ($this->type == 'loi')
	$this->titre .= ' en hémicycle';
      else if ($this->type == 'commission')
	$this->titre .= ' en commissions';
    }
    $this->response->setTitle($this->titre.' de '.$this->parlementaire->nom." - NosDéputés.fr");
    $this->interventions->orderBy('i.date DESC, i.timestamp ASC');
    if ($this->type === "all") {
      $this->rss = true;
      $request->setParameter('rss', array(array('link' => '@parlementaire_interventions_rss?slug='.$this->parlementaire->slug, 'title'=>'Les dernières interventions de '.$this->parlementaire->nom.' en RSS')));
    }
  }

  public function executeParlementaireOrganisme(sfWebRequest $request) {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->orga = Doctrine::getTable('Organisme')->find($request->getParameter('orga'));
    $this->forward404Unless($this->orga);
    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');
    $this->interventions = Doctrine::getTable('Intervention')->createQuery('i')
      ->leftJoin('i.Seance s')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('s.organisme_id = ?', $this->orga)
      ->orderBy('i.date DESC, i.timestamp ASC');
    $this->surtitre = link_to($this->orga->getNom(), '@list_parlementaires_organisme?slug='.$this->orga->getSlug());
    $this->titre = 'Interventions';
    $this->response->setTitle($this->titre.' en '.$this->orga->nom.' de '.$this->parlementaire->nom." - NosDéputés.fr");
  }

  public function executeShow(sfWebRequest $request) {
    $this->intervention = Doctrine::getTable('Intervention')->createquery('i')
      ->where('i.id = ?', $request->getParameter('id'))
      ->fetchOne();
    $this->forward404Unless($this->intervention);

    $titre = "";
    $this->section = $this->intervention->getSection();
    $this->secparent = $this->section->getSection();
    $this->seance = $this->intervention->getSeance();
    if ($this->intervention->getType() == 'commission') {
      $this->orga = $this->seance->getOrganisme();
      $titre .= $this->orga->getNom();
    } else if ($this->secparent && !(preg_match('/questions/i', $this->secparent->getTitre())))
      $titre .= $this->secparent->getTitre();
    else
      $titre .= $this->section->getTitre();
    $titre .= " - ".$this->seance->getTitre(0);
    $this->lois = $this->intervention->getTags(array('is_triple' => true,
						     'namespace' => 'loi',
						     'key' => 'numero',
						     'return'    => 'value'));
    $this->amdmts = $this->intervention->getTags(array('is_triple' => true,
						       'namespace' => 'loi',
						       'key' => 'amendement',
						       'return'    => 'value'));
    $this->response->setTitle($titre.' - Intervention de '.$this->intervention->getIntervenant()->nom." - NosDéputés.fr");
    //    $this->response->setDescription($this->intervention->intervention);
  }

  private function getSectionId(sfWebRequest $request) {
    $section_id = $request->getParameter('dossier', '');
    if ($section_id != "") {
      if (preg_match('/[a-z]/', $section_id)) {
        $section_id = Doctrine::getTable('Section')->findOneByIdDossierAn(strtolower($section_id));
        //$this->forward404Unless($section_id);
        $section_id = $section_id->id;
      }
    }
    return $section_id;
  }

  public function executeListSeancesByLoi(sfWebRequest $request) {
    $loi_id = $request->getParameter('loi');
    $this->forward404Unless($loi_id);
    $dossier = $this->getSectionId($request);
    $extrajoin = "";
    if ($dossier) {
      $extrajoin = " left join section sc on i.section_id = sc.id";
    }
    $this->query = new Doctrine_RawSql();
    $this->query->select('distinct({s.id})')
      ->from('seance s left join intervention i on s.id = i.seance_id left join tagging tg on tg.taggable_model = "Intervention" and tg.taggable_id = i.id left join tag t on tg.tag_id = t.id'.$extrajoin)
      ->addComponent('s', 'Seance s')
      ->where('t.triple_namespace = "loi" and t.triple_key = "numero" and t.triple_value = ?', $loi_id);
    if ($dossier) {
      $this->query->addWhere('(sc.section_id = ? OR sc.id = ?)', array($dossier, $dossier));
    }
    if ($request->getParameter('commission')) {
      $this->query->addWhere('s.type = "commission"');
    }
    myTools::templatize($this, $request, 'nosdeputes.fr_seances_'.$loi_id.'_'.$dossier);
    $this->res = array('seances' => array());
    $this->breakline = 'seance';
    $this->champs = array('seance' => 'seance');
    foreach ($this->query->execute() as $s) {
      $this->res['seances'][] = array('seance' => $s['id']);
    }
  }

  private function initSeance(sfWebRequest $request) {
    $seance_id = $request->getParameter('seance');
    $this->seance = Doctrine::getTable('Seance')->find($seance_id);
    $this->forward404Unless($this->seance);
    if ($this->seance->type == 'commission') $this->orga = $this->seance->getOrganisme();
    $query = Doctrine::getTable('Intervention')->createquery('i')
        ->where('i.seance_id = ?', $seance_id)
        ->orderBy('i.timestamp ASC');


    $parlementaires = Doctrine::getTable('Intervention')->createquery('i')
        ->where('i.seance_id = ?', $seance_id)
        ->leftJoin('i.Parlementaire p')
        ->groupBy('i.parlementaire_id')
        ->execute()
        ;
    $this->parlementaires = array();
    foreach ($parlementaires as $p) {
      if ($p->parlementaire_id) {
        $this->parlementaires[$p->parlementaire_id] = $p->Parlementaire;
      }
    }

    $personnalites = Doctrine::getTable('Intervention')->createquery('i')
        ->where('i.seance_id = ?', $seance_id)
        ->leftJoin('i.Personnalite p')
        ->groupBy('i.personnalite_id')
        ->execute()
        ;
    $this->personnalites = array();
    foreach ($personnalites as $p) {
      if ($p->personnalite_id) {
        $this->personnalites[$p->personnalite_id] = $p->Personnalite;
      }
    }

    $sects = Doctrine::getTable('Intervention')->createquery('i')
        ->where('i.seance_id = ?', $seance_id)
        ->leftJoin('i.Section s')
        ->groupBy('i.section_id')
        ->execute();
    $this->sections = array();
    foreach ($sects as $s) {
	if ($s->section_id) {
	   $sec = $s->Section;
	   $this->sections[$s->section_id] = $sec;
	   if ($sec->id != $sec->section_id && !isset($this->sections[$sec->section_id])) {
	       $this->sections[$sec->section_id] = $sec->Section;
	   }
        }
    }

    return $query;
  }

  public function executeSeanceAPI(sfWebRequest $request) {
    $this->query = $this->initSeance($request);
    if ($section_id = $this->getSectionId($request)) {
      $this->query->leftJoin('i.Section s')->addWhere('s.section_id = ? OR s.id = ?', array($section_id, $section_id));
    }
    myTools::templatize($this, $request, 'nosdeputes.fr_seance'.$this->seance->id.'_'.$this->seance->updated_at);
    $this->interventions = $this->query->fetchArray();
    $this->res = array('seance' => array());
    $this->breakline = 'intervention';
    $this->multi = array('tag' => 'tag', 'loi' => 'loi', 'amendement' => 'amendement');
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
    foreach($this->interventions as $int) {
      $i['seance_id'] = $int['seance_id'];
      $i['seance_titre'] = $this->seance->titre;
      $i['seance_lieu'] = ($this->orga) ? $this->orga->getNom() : 'Hémicycle';
      $i['date'] = $int['date'];
      $i['heure'] = $this->seance->moment;
      $i['type'] = $int['type'];
      $i['timestamp'] = $int['timestamp'];
      $i['section'] = '';
      $i['soussection'] = '';
      if ($int['section_id']) {
        if ($this->sections[$int['section_id']]->section_id) {
          $i['section'] = $this->sections[$this->sections[$int['section_id']]->section_id]->titre;
        }else{
          $i['section'] = $this->sections[$int['section_id']]->titre;
        }
        $i['soussection'] = $this->sections[$int['section_id']]->titre;
      }
      $i['intervenant_nom'] = '';
      $i['intervenant_fonction'] = $int['fonction'];
      $i['intervenant_slug'] = '';
      $i['intervenant_groupe'] = '';
      if ($int['parlementaire_id']) {
        $i['intervenant_nom'] = $this->parlementaires[$int['parlementaire_id']]->getNom();
        $i['intervenant_slug'] = $this->parlementaires[$int['parlementaire_id']]->getSlug();
        $i['intervenant_groupe'] = $this->parlementaires[$int['parlementaire_id']]->getGroupeAcronyme();
      }else if ($int['personnalite_id']) {
        $i['intervenant_nom'] = $this->personnalites[$int['personnalite_id']]->getNom();
      }
      $i['nbmots'] = $int['nb_mots'];
      $i['contenu'] = $int['intervention'];
      $qtag = Doctrine::getTable('tag')->createQuery('t');
      $qtag->from('Tagging tg, tg.Tag t');
      $qtag->andWhere('tg.taggable_id = ?', $int['id']);
      $qtag->andWhere('tg.taggable_model = "Intervention"');
      $tags = array();
      $lois = array();
      $amendements = array();
      foreach($qtag->fetchArray() as $tag) {
        if ($tag['Tag']['triple_namespace'] == 'loi') {
          if ($tag['Tag']['triple_key'] == 'numero') {
            $lois[] = $tag['Tag']['triple_value'];
          }else if ($tag['Tag']['triple_key'] == 'amendement') {
            $amendements[] = $tag['Tag']['triple_value'];
          }
        }else{
          $tags[] = $tag['Tag']['name'];
        }
      }
      $i['tags'] = myTools::array2hash($tags, 'tag');
      $i['amendements'] = myTools::array2hash($amendements, 'amendement');
      $i['lois'] = myTools::array2hash($lois, 'loi');
      $i['source'] = $int['source'];
      $i['url_nosdeputes'] = url_for('@interventions_seance?seance='.$int['seance_id'], 'absolute=true')."#inter_".$int['md5'];
      $i['url_nosdeputes_api'] = url_for("@api_document?class=Intervention&id=".$int['id']."&format=".$request->getParameter('format'), 'absolute=true');
      $i['id'] = $int['id'];
      $this->res['seance'][] = array('intervention' => $i);
      if (!isset($this->champs)) {
        $this->champs = array();
        foreach($i as $k => $v) {
          $this->champs[$k] = $k;
        }
      }
    }
  }

  public function executeSeance(sfWebRequest $request) {
    $query = $this->initSeance($request);
    $this->interventions = $query->execute();

    $qtag = Doctrine_Query::create();
    $qtag->from('Tagging tg, tg.Tag t, Intervention i');
    $qtag->where('i.seance_id = ?', $this->seance->id);
    $qtag->andWhere('i.id = tg.taggable_id');
    $qtag->andWhere('t.name NOT LIKE ?', 'loi:%');
    $this->tags = PluginTagTable::getPopulars($qtag, array('model' => 'Intervention', 'limit' => 9));

  }

  public function executeTag(sfWebRequest $request) {
    $this->tags = explode('\|', $request->getParameter('tags'));

    if (Doctrine::getTable('Tag')->findOneByName($this->tags[0]))
      $query = PluginTagTable::getObjectTaggedWithQuery('Intervention', $this->tags);
    else
      $query = Doctrine::getTable('Intervention')
	->createQuery('Intervention')->where('0');

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = Doctrine::getTable('Parlementaire')
	->findOneBySlug($slug);
      if ($this->parlementaire)
	$query->andWhere('Intervention.parlementaire_id = ?', $this->parlementaire->id)
      ;
    }

    if ($section = $request->getParameter('section')) {
      $query->andWhere('(Intervention.section_id = ? OR si.section_id = ?)', array($section, $section))
	->leftJoin('Intervention.Section si');
    }

    $query->orderBy('Intervention.date DESC, Intervention.timestamp ASC');
    $this->query = $query;
  }

  public function executeSearch(sfWebRequest $request) {
    $this->mots = $request->getParameter('search');
    $mots = $this->mots;
    $mcle = array();

    if (preg_match_all('/("[^"]+")/', $mots, $quotes)) {
      foreach(array_values($quotes[0]) as $q)
	$mcle[] = '+'.$q;
      $mots = preg_replace('/\s*"([^\"]+)"\s*/', ' ', $mots);
    }

    foreach(split(' ', $mots) as $mot) {
      if ($mot && !preg_match('/^[\-\+]/', $mot))
	$mcle[] = '+'.$mot;
    }

    $this->high = array();
    foreach($mcle as $m) {
      $this->high[] = preg_replace('/^[+-]"?([^"]*)"?$/', '\\1', $m);
    }

    $sql = 'SELECT i.id FROM intervention i WHERE MATCH (i.intervention) AGAINST (\''.str_replace("'", "\\'", implode(' ', $mcle)).'\' IN BOOLEAN MODE)';

    $search = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();

    $ids = array();
    foreach($search as $s) {
      $ids[] = $s['id'];
    }

    $this->query = Doctrine::getTable('Intervention')->createQuery('i');
    if (count($ids))
      $this->query->whereIn('i.id', $ids);
    else if (count($mcle))
      foreach($mcle as $m)
	$this->query->andWhere('i.intervention LIKE ?', '% '.$m.' %');
    else {
      $this->query->where('0');
      return ;
    }

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = Doctrine::getTable('Parlementaire')
	->findOneBySlug($slug);
      if ($this->parlementaire)
	$this->query->andWhere('i.parlementaire_id = ?', $this->parlementaire->id);
    }

    if ($section = $request->getParameter('section')) {
      $this->query->andWhere('(Intervention.section_id = ? OR si.section_id = ?)', array($section, $section))
	->leftJoin('i.Section si');
    }
    $this->query->orderBy('date DESC, timestamp ASC');
    if ($request->getParameter('rss')) {
      $this->setTemplate('rss');
      $this->feed = new sfRssFeed();
      $this->feed->setLanguage('fr');
    } else $request->setParameter('rss', array(array('link' => '@search_interventions_mots_rss?search='.$this->mots, 'title'=>'Les dernières interventions sur '.$this->mots.' en RSS')));

  }
}

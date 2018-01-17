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

  public function executeDocument(sfWebRequest $request)
  {
    $class = $request->getParameter('class');
    $format = $request->getParameter('format');
    $id = $request->getParameter('id');
    $this->forward404Unless($class);
    $o = doctrine::getTable($class)->find($id);
    $this->forward404Unless($o);
    if ($class == 'Parlementaire') {
      return $this->redirect('api/parlementaire?slug='.$o->slug.'&format='.$format);
    }
    $slug = $class.'_'.$id;
    $date = $o->updated_at;
    $this->res = array();
    $this->res[strtolower($class)] = $o->toArray();
    if ($o->getLink())
        $this->res[strtolower($class)]['url_nosdeputes'] = trim(sfConfig::get('app_base_url'), '/').$o->getLink();

    $this->breakline = strtolower($class);
    myTools::templatize($this, $request, 'nosdeputes.fr_'.'_'.$slug.'_'.$date);
  }

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
      $depute = array();
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
    myTools::templatize($this, $request, 'nosdeputes.fr_'.$date.'_stats_deputes');
  }

  public function executeTopSynthese(sfWebRequest $request) {
    $format = $request->getParameter('format');
    $this->withBOM = $request->getParameter('withBOM');

    $fin = myTools::isFinLegislature();
    $parlementaires = Doctrine::getTable("Parlementaire")->prepareParlementairesTopQuery($fin)->execute();

    $this->champs = array("id" => 1);
    $this->multi = array();
    $this->multi["site"] = 1;

    $this->res = array();
    foreach($parlementaires as $p) {
      $tops = $p->top;

      // En mode bilan final on n'affiche que les députés avec plus de 6 mois de mandat
      if ($fin && $tops['nb_mois'] < 6)
        continue;

      $parl = self::getParlementaireArray($p, $format, 2);

      // Liste des champs pour le csv
      foreach(array_keys($parl) as $k)
        if (!isset($this->champs[$k]))
          $this->champs[$k] = 1;

      // ajout du nombre de mois de mandat en mode bilan
      if ($fin) {
        $parl["nb_mois"] = $tops['nb_mois'];
        if (!isset($this->champs["nb_mois"]))
          $this->champs["nb_mois"] = 1;
      }

      // Traite chaque indicateur
      foreach(array_keys($tops) as $k) {
        if ($k == "nb_mois")
          continue;

        // Gestion de l'ordre des parametres
        $kfinal = preg_replace('/^\d*_/', '', $k);
        $parl[$kfinal] = $tops[$k]['value'];
        if (!isset($this->champs[$kfinal]))
          $this->champs[$kfinal] = 1;

        // Valeur moyenne en mode bilan
        if ($fin) {
          $kmean = $kfinal.'_moyenne_mensuelle';
          $parl[$kmean] = $tops[$k]['moyenne'];
          if (!isset($this->champs[$kmean]))
            $this->champs[$kmean] = 1;
        }
      }
      $this->res["deputes"][] = array('depute' => $parl);
    }

    // Complète les valeurs de champs manquantes
    for($i=0; $i < count($this->res["deputes"]); $i++) {
      foreach(array_keys($this->champs) as $key) {
        if (!isset($this->res['deputes'][$i]['depute'][$key])) {
          $this->res['deputes'][$i]['depute'][$key] = 0;
        }
      }
    }

    $this->breakline = 'depute';
    myTools::templatize($this, $request, 'nosdeputes.fr_synthese_'.date('Y-m-d'));
  }

  public function executeListOrganismes(sfWebRequest $request) {
    $type = $request->getParameter('type');
    $this->forward404Unless($type == "extra" || $type == "groupes" || $type == "parlementaire" || $type == "groupe");
    $query = Doctrine::getTable('Organisme')->createQuery('o')
      ->innerJoin('o.ParlementaireOrganisme po')
      ->where('o.type = ?', $type)
      ->groupBy('o.id')
      ->orderBy('o.nom');
    $orgas = $query->execute();
    $this->champs = array();
    $this->res = array('organismes' => array());
    $this->breakline = 'organisme';

    $curgpes = myTools::getCurrentGroupes();
    $colormap = myTools::getGroupesColorMap();
    $groupesorder = myTools::getGroupesOrderMap();
    foreach($orgas as $o) {
      $orga = array();
      $orga['id'] = $o->id * 1;
      $orga['slug'] = $o->slug;
      $orga['nom'] = $o->nom;
      if ($o->type == "groupe") {
        $acro = $o->getSmallNomGroupe();
        $orga['acronyme'] = $acro;
        $orga['groupe_actuel'] = in_array($acro, $curgpes);
        $orga['couleur'] = (isset($colormap[$acro]) ? $colormap[$acro] : '');
        $orga['order'] = (isset($groupesorder[$acro]) ? $groupesorder[$acro] : '');
      }
      $orga['type'] = $o->type;
      $orga['url_nosdeputes'] = myTools::url_forAPI('@list_parlementaires_organisme?slug='.$o->slug);
      $orga['url_nosdeputes_api'] = myTools::url_forAPI('@list_parlementaires_organisme_api?format='.$request->getParameter('format').'&orga='.$o->slug);
      if ($request->getParameter('format') == 'csv')
       foreach(array_keys($orga) as $key)
        if (!isset($this->champs[$key]))
         $this->champs[$key] = 1;
      $this->res['organismes'][] = array('organisme' => $orga);
    }
    myTools::templatize($this, $request, 'nosdeputes.fr_organismes'.date('Y-m-d'));
  }

  public function executeListParlementairesGroupe(sfWebRequest $request) {
    $acro = strtolower($request->getParameter('acro'));
    $nom = Organisme::getNomByAcro($acro);
    $this->forward404Unless($nom);
    $orga = Doctrine::getTable('Organisme')->findOneByNom($nom);
    $this->forward404Unless($orga);
    $request->setParameter('orga', $orga->slug);
    $this->executeListParlementaires($request);
  }

  public function executeListParlementaires(sfWebRequest $request) {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->multi = array();
    if ($request->getParameter('current') == true) {
      $query->where('fin_mandat IS NULL OR debut_mandat > fin_mandat');
      $this->multi['responsabilite'] = 1;
      $this->multi['mandat'] = 1;
    }
    $this->multi['site'] = 1;
    $this->multi['email'] = 1;
    $this->multi['adresse'] = 1;
    $this->multi['collaborateur'] = 1;
    $orga = $request->getParameter('orga');
    if ($orga) {
      $includePast = ($request->getParameter('includePast') == true);
      $this->forward404Unless(Doctrine::getTable('Organisme')->findOneBySlug($orga));
      $query->leftJoin('p.ParlementaireOrganisme po, po.Organisme o')
        ->addWhere('o.slug = ?', $orga);
      if (!$includePast) {
      // Par défaut on ne renvoie que les députés actuellement membres
        $query->addWhere('po.fin_fonction IS NULL')
          ->addOrderBy('po.importance DESC, p.nom_de_famille');
      } else // Autrement on renvoie aussi les anciens membres
        $query->addOrderBy('po.fin_fonction, po.importance DESC, p.nom_de_famille');
    }
    $deputes = $query->execute();
    $this->champs = array();
    $this->res = array('deputes' => array());
    $this->breakline = 'depute';
    foreach($deputes as $dep) {
      $depute = self::getParlementaireArray($dep, $request->getParameter('format'), ($request->getParameter('current') == true || !$orga ? 1 : 2));
      if ($orga) {
        $depute['fonction'] = $dep['ParlementaireOrganisme'][0]['fonction'];
        $depute['debut_fonction'] = $dep['ParlementaireOrganisme'][0]['debut_fonction'];
        if ($includePast) {
        // Utile uniquement si on renvoie aussi les anciens membres
          $depute['fin_fonction'] = $dep['ParlementaireOrganisme'][0]['fin_fonction'];
          $depute['groupe_a_fin_fonction'] = ($depute['fin_fonction'] ? $dep['ParlementaireOrganisme'][0]['parlementaire_groupe_acronyme'] : "");
        }
      }
      if ($request->getParameter('format') == 'csv')
       foreach(array_keys($depute) as $key)
        if (!isset($this->champs[$key]))
         $this->champs[$key] = 1;
      $this->res['deputes'][] = array('depute' => $depute);
    }
    myTools::templatize($this, $request, 'nosdeputes.fr_deputes'.($orga ? "_".$orga : "").($request->getParameter('current') == true ? "_en_mandat" : "").'_'.date('Y-m-d'));
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($slug);
    $depute = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    if (!$depute) {
      $depute = Doctrine::getTable('Parlementaire')->findOneByNomSexeGroupeCirco($slug);
      if ($depute)
        return $this->redirect('api/parlementaire?slug='.$depute->slug.'&format='.$request->getParameter('format'));
    }
    $this->forward404Unless($depute);
    $this->res = array();
    $this->res['depute'] = self::getParlementaireArray($depute, $request->getParameter('format'));
    $this->multi = array();
    $this->multi['responsabilite'] = 1;
    $this->multi['email'] = 1;
    $this->multi['adresse'] = 1;
    $this->multi['collaborateur'] = 1;
    $this->multi['mandat'] = 1;
    $this->multi['site'] = 1;
    $this->breakline = 'depute';
    $date = $depute->updated_at.'';
    $date = preg_replace('/[- :]/', '', $date);
    myTools::templatize($this, $request, 'nosdeputes.fr_'.'_'.$slug.'_'.$date);
  }

  public static function getParlementaireArray($parl, $format, $light = 0) {
    $res = array();
    if (!$parl)
	throw new Exception("pas de parlementaire");
    $res['id'] = $parl->id * 1;
    $res['nom'] = $parl->nom;
    $PrNoPaNP = $parl->getPrenomNomParticule();
    $res['nom_de_famille'] = $PrNoPaNP[3];
    $res['prenom'] = $PrNoPaNP[0];
    $res['sexe'] = $parl->sexe;
    $res['date_naissance'] = $parl->date_naissance;
    $res['lieu_naissance'] = $parl->lieu_naissance;
    $res['num_deptmt'] = $parl->getNumDepartement();
    $res['nom_circo'] = $parl->nom_circo;
    $res['num_circo'] = $parl->num_circo * 1;
    $res['mandat_debut'] = $parl->debut_mandat;
    if ($parl->fin_mandat)
      $res['mandat_fin'] = $parl->fin_mandat;
    else if ($format == 'csv')
      $res['mandat_fin'] = "";
    if (!$parl->isEnMandat())
      $res['ancien_depute'] = 1;
    else if ($format == 'csv')
      $res['ancien_depute'] = 0;
    if (!$light) {
      $groupe = $parl->getGroupe();
      if (is_object($groupe))
        $res['groupe'] = myTools::array2hash($groupe, 'groupe_politique');
      else if ($format == 'csv')
        $res['groupe'] = "";
    }
    $res['groupe_sigle'] = $parl->groupe_acronyme;
    if (!$parl->parti)
      $parl->parti = "";
    $res['parti_ratt_financier'] = $parl->parti;
    if (!$light) {
      $res['responsabilites'] = myTools::array2hash($parl->getResponsabilites(), 'responsabilite');
      $res['responsabilites_extra_parlementaires'] = myTools::array2hash($parl->getExtras(), 'responsabilite');
      $res['groupes_parlementaires'] = myTools::array2hash($parl->getGroupes(), 'responsabilite');
      $res['historique_responsabilites'] = myTools::array2hash($parl->getHistorique(), 'responsabilite');
    }
    $res['sites_web'] = myTools::array2hash(unserialize($parl->sites_web), 'site');
    if ($light != 2) {
      $res['emails'] = myTools::array2hash(unserialize($parl->mails), 'email');
      $res['adresses'] = myTools::array2hash(unserialize($parl->adresses), 'adresse');
      $res['collaborateurs'] = myTools::array2hash(unserialize($parl->collaborateurs), 'collaborateur');
      $res['anciens_mandats'] = myTools::array2hash(unserialize($parl->anciens_mandats), 'mandat');
      $res['autres_mandats'] = myTools::array2hash(unserialize($parl->autres_mandats), 'mandat');
      $res['anciens_autres_mandats'] = myTools::array2hash(unserialize($parl->anciens_autres_mandats), 'mandat');
    }
    $res['profession'] = $parl->profession;
    $res['place_en_hemicycle'] = $parl->place_hemicycle;
    $res['url_an'] = $parl->url_an;
    $res['id_an'] = $parl->id_an;
    $res['slug'] = $parl->getSlug();
    $res['url_nosdeputes'] = myTools::url_forAPI('@parlementaire?slug='.$res['slug']);
    $res['url_nosdeputes_api'] = myTools::url_forAPI('api/parlementaire?format='.$format.'&slug='.$res['slug']);
    $res['nb_mandats'] = count(unserialize($parl->getAutresMandats()));
    $res['twitter'] = "";
    if ($parl->sites_web) foreach (unserialize($parl->sites_web) as $site)
      if (preg_match("/twitter.com/", $site))
        $res['twitter'] = str_replace("https://twitter.com/", "", $site);
    return $res;
  }

  public function executeListSections(sfWebRequest $request) {
    $query = Doctrine::getTable('Section')
      ->createQuery()
      ->where('id = section_id')
      ->andWhere('id_dossier_an IS NOT NULL');

    $order = $request->getParameter('order', 'plus');
    if ($order == 'date') {
      $query->orderBy('max_date DESC');
    } else if ($order == 'plus') {
      $query->orderBy('nb_interventions DESC');
    } else if ($order == 'coms') {
      $query->orderBy('nb_commentaires DESC');
    } else if ($order == 'nom') {
      $query->orderBy('titre');
    } else $this->forward404();

    $this->champs = array();
    $format = $request->getParameter('format');

    $this->res = array('sections' => array());
    foreach($query->execute() as $sec) {
      $section = self::getSectionArray($sec, $format, 1);
      $this->res['sections'][] = array('section' => $section);
    }

    if (count($this->res['sections']) && ($format == 'csv'))
      foreach(array_keys($this->res['sections'][0]) as $key)
        if (!isset($this->champs[$key]))
          $this->champs[$key] = 1;

    $this->breakline = 'section';
    myTools::templatize($this, $request, 'nosdeputes.fr_dossiers_'.$order.'_'.date('Y-m-d'));
  }

  public function executeSection(sfWebRequest $request)
  {
    $secid = $request->getParameter('id');
    $this->forward404Unless($secid);

    if ($option = Doctrine::getTable('VariableGlobale')->findOneByChamp('linkdossiers')) {
      $links = unserialize($option->getValue());
      if (isset($links[$secid]))
        $secid = $links[$secid];
    }

    // Recherche par id ou par id dossier AN
    $section = Doctrine::getTable('Section')->find($secid);
    if (!$section) {
      $section = Doctrine::getTable('Section')->findOneByIdDossierAn($secid);
    }
    $this->forward404Unless($section);

    $format = $request->getParameter('format');
    $this->res = array('section' => self::getSectionArray($section, $format, ($format == 'csv' ? 1 : 0)));

    $this->breakline = 'section';
    myTools::templatize($this, $request, 'nosdeputes.fr_dossier_'.$secid.'_'.date('Y-m-d'));
  }

  public static function getSectionArray($sec, $format, $light=0)
  {
    $res = array();
    if (!$sec)
      throw new Exception("pas de section");

    $res['id'] = $sec->id * 1;
    $res['id_dossier_institution'] = $sec->id_dossier_an;
    $res['titre'] = $sec->titre;
    $res['min_date'] = substr($sec->min_date, 0, 10);
    $res['max_date'] = $sec->max_date;
    $res['nb_interventions'] = $sec->nb_interventions * 1;
    $res['url_institution'] = $sec->getLinkSource();
    $res['url_nosdeputes'] = myTools::url_forAPI('@section?id='.$res['id']);
    $res['url_nosdeputes_api'] = myTools::url_forAPI('@section_'.$format.'?id='.$res['id']);

    if ($light == 0) {
      // List related Séances
      $seances = array();
      foreach ($sec->getSeances() as $seance) {
        $seances[] = self::getSeanceArray($seance, $format);
      }
      $res['seances'] = myTools::array2hash($seances, 'seance');

      // List related law texts & reports
      $lois = $sec->getTags(array('is_triple' => true,
                                  'namespace' => 'loi',
                                  'key' => 'numero',
                                  'return' => 'value'));
      $docs = array();
      if ($sec->id_dossier_an || $lois) {
        $qtextes = Doctrine_Query::create()
          ->select('t.id, t.numero, t.annexe, t.type, t.type_details, t.titre, t.date, t.source, t.signataires')
          ->from('Texteloi t')
          ->orderBy('t.numero, t.annexe');
        if ($lois)
          $qtextes->orWhereIn('t.numero', $lois);
        if ($sec->id_dossier_an)
          $qtextes->orWhere('t.id_dossier_an = ?', $sec->id_dossier_an);
        foreach ($qtextes->fetchArray() as $texte)
          $docs[$texte['id']] = $texte;
      }
      foreach ($lois as $loi)
        if (!isset($docs["$loi"]))
          $docs["$loi"] = 1;

      $documents = array();
      foreach ($docs as $id => $item) {
        if ($item == 1) {
          $item = array('id' => $id);
        } else {
          $item['url_nosdeputes'] = myTools::url_forAPI('@document?id='.$id);
          $item['url_nosdeputes_api'] = myTools::url_forAPI('@api_document?class=Texteloi&id='.$id.'&format='.$format);
        }
        $documents[] = $item;
      }
      $res['documents'] = myTools::array2hash($documents, 'document');

      // List speakers
      $interv_parl = array();
      $query = Doctrine::getTable('Intervention')->createQuery('i')
        ->select('p.nom, p.slug, i.id, count(i.id)')
        ->leftJoin('i.Parlementaire p, i.Section s')
        ->andWhere('((i.fonction != ? AND i.fonction != ? ) OR i.fonction IS NULL)', array('président', 'présidente'))
        ->andWhere('i.parlementaire_id IS NOT NULL')
        ->andWhere('i.nb_mots > 20')
        ->groupBy('p.id')
        ->orderBy('count DESC');
      if ($sec->id == $sec->section_id)
        $query->andWhere('s.section_id = ?', $sec->id);
      else $query->andWhere('s.id = ?', $sec->id);
      foreach ($query->fetchArray() as $parl)
        $interv_parl[] = self::getParlementaireInterventionsArray($parl, $format, $sec->section_id);
      $res['intervenants'] = myTools::array2hash($interv_parl, 'parlementaire');

      // List subsections
      $subsections = array();
      foreach($sec->getSubSections() as $subsection) {
        if ($sec->id != $subsection->id) {
          $sub = array();
          $sub['id'] = $subsection->id;
          $sub['titre'] = $subsection->titre;
          $sub['min_date'] = substr($subsection->min_date, 0, 10);
          $sub['max_date'] = $subsection->max_date;
          $sub['timestamp'] = $subsection->timestamp;
          $sub['url_nosdeputes'] = myTools::url_forAPI('@section?id='.$subsection->id);
          $sub['url_nosdeputes_api'] = myTools::url_forAPI('@section_'.$format.'?id='.$subsection->id);

          $subsections[] = $sub;
        }
      }
      $res['soussections'] = myTools::array2hash($subsections, 'soussection');
    }

    return $res;
  }

  public static function getSeanceArray($seance, $format) {
    if (!$seance)
      throw new Exception("pas de seance");

    $res = array();
    $res['id'] = $seance->id;
    $res['type'] = $seance->type;
    $res['date'] = $seance->date;
    $res['heure'] = $seance->moment;
    $res['session'] = $seance->session;
    $res['organisme'] = $seance->getOrganisme()->nom;
    $res['url_nosdeputes'] = myTools::url_forAPI('@interventions_seance?seance='.$seance->id);
    $res['url_nosdeputes_api'] = myTools::url_forAPI('@interventions_seance_api?format='.$format.'&seance='.$seance->id);

    return $res;
  }

  public static function getParlementaireInterventionsArray($parl, $format, $secid) {
    if (!$parl)
      throw new Exception("pas de parlementaire");

    $res = array();
    $res['nom'] = $parl['Parlementaire']['nom'];
    $res['slug'] = $parl['Parlementaire']['slug'];
    $res['nb_interventions'] = $parl['count'];
    $res['url_nosdeputes'] = myTools::url_forAPI('@parlementaire_texte?slug='.$res['slug'].'&id='.$secid);

    return $res;
  }

  public function executeAmendements(sfWebRequest $request) {
    chdir(sfConfig::get('sf_root_dir'));
    $this->task = new printDumpAmendementsLoiTask($this->dispatcher, new sfFormatter());
    $this->loi = preg_replace('/[^0-9a-z\-]/i', '', $request->getParameter('loi'));
    $this->format = preg_replace('/[^a-z]/', '', $request->getParameter('format'));
    $this->setLayout(false);
    myTools::headerize($this, $request, 'nosdeputes.fr_amendements_'.$this->loi, false);
  }

  public function executeLinksAmendements(sfWebRequest $request) {
    $id = $request->getParameter('loi');
    $amdmts = Doctrine_Query::create()
      ->select('pa.parlementaire_id, pa.parlementaire_groupe_acronyme, pa.amendement_id, a.content_md5 as uniq_key, a.sujet as amendement_sujet, a.sort as amendement_sort')
      ->from('ParlementaireAmendement pa')
      ->innerJoin('pa.Amendement a')
      ->where('a.texteloi_id = ?', $id)
      ->andWhere('a.sort <> ?', "Rectifié")
      ->orderBy('a.id')
      ->fetchArray();
    $parls = array();
    $this->links = array();
    $sorts = array();
    $sujets = array();
    foreach ($amdmts as $pa) {
      if (!isset($sorts[$pa['amendement_sort']]))
        $sorts[$pa['amendement_sort']] = 1;
      if (!isset($sorts[$pa['amendement_sujet']]))
        $sorts[$pa['amendement_sujet']] = 1;
      if (!isset($parls[$pa['parlementaire_id']]))
        $parls[$pa['parlementaire_id']] = array('a' => 1, 'g' => $pa['parlementaire_groupe_acronyme']);
      else $parls[$pa['parlementaire_id']]['a'] += 1;
    }
    foreach (Doctrine_Query::create()->select('p.id, p.nom, p.slug, p.groupe_acronyme, p.place_hemicycle')->from('Parlementaire p')->whereIn('p.id', array_keys($parls))->fetchArray() as $parl) {
      $parls[$parl['id']]['n'] = $parl['nom'];
      $parls[$parl['id']]['s'] = $parl['slug'];
      if (!$parls[$parl['id']]['g'])
        $parls[$parl['id']]['g'] = $parl['groupe_acronyme'];
      $parls[$parl['id']]['p'] = $parl['place_hemicycle'];
    }
    $curra = 0;
    $prevkey = 0;
    $ident = array();
    foreach ($amdmts as $pa) {
      if ($curra != $pa['amendement_id']) {
        if ($prevkey) {
          if (!isset($ident[$prevkey]))
            $ident[$prevkey] = $cosign;
          else $ident[$prevkey] = array_merge($ident[$prevkey], $cosign);
        }
        $prevkey = $pa['uniq_key'];
        $curra = $pa['amendement_id'];
        $cosign = array();
      } else foreach ($cosign as $co)
        $this->addLink($pa, $co, 2);
      $cosign[] = $pa['parlementaire_id'];
      if (isset($ident[$pa['uniq_key']]))
        foreach ($ident[$pa['uniq_key']] as $i)
          $this->addLink($pa, $i, 1);
    }
    $this->res = array('sorts' => $sorts, 'sujets' => $sujets, 'parlementaires' => $parls, 'links' => array_values($this->links));

    myTools::templatize($this, $request, 'nosdeputes.fr_amendements_'.$id.'_'.date('Y-m-d'));
  }

  private function addLink($parl_amdt, $parl, $weight) {
    $lid = $parl_amdt['uniq_key'].'-'.min($parl_amdt['parlementaire_id'], $parl).'-'.max($parl_amdt['parlementaire_id'], $parl);
    if (!isset($this->links[$lid]))
      $this->links[$lid] = array('1' => $parl_amdt['parlementaire_id'], '2' => $parl, 'w' => $weight);
    else $this->links[$lid]['w'] += $weight;
  }

}

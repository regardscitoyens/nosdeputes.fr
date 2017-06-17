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
    if ($class == 'Parlementaire') {
      return $this->redirect('api/parlementaire?slug='.$o->slug.'&format='.$format);
    }
    $slug = $class.'_'.$id;
    $date = $o->updated_at;
    $this->res = array();
    $this->res[strtolower($class)] = $o->toArray();
    if ($o->getLink())
        $this->res[strtolower($class)]['url_nosdeputes'] = trim(sfConfig::get('app_base_url'), '/').$o->getLink();

    $this->breakline = '';
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
    $qp = Doctrine::getTable('Parlementaire')->createQuery('p');
    $fin = myTools::isFinLegislature();
    if (!$fin) $qp->andWhere('fin_mandat IS NULL');
    $dixmois = time() - round(60*60*24*3650/12);
    if ($dixmois > strtotime(myTools::getDebutLegislature()))
      $qp->andWhere('debut_mandat < ?', date('Y-m-d', $dixmois));
    $qp->orderBy('nom_de_famille');
    $parlementaires = $qp->execute();
    unset($qp);
    $this->res = array();
    $this->champs = array();
    $this->multi = array();
    $this->multi["site"] = 1;
    foreach($parlementaires as $p) {
      $tops = $p->top;
      $depute['id'] = $p->id;
      $this->champs['id'] = 1;
      if ($fin && $tops['nb_mois'] < 4)
        continue;
      $depute = $this->getParlementaireArray($p, $format, 2);
      if ($fin)
        $depute["nb_mois"] = $tops['nb_mois'];
      if ($format == 'csv')
       foreach(array_keys($depute) as $key)
        if (!isset($this->champs[$key]))
         $this->champs[$key] = 1;
      foreach(array_keys($tops) as $k) {
        if ($k != 'nb_mois') {
          //Gestion de l'ordre des parametres
          $kfinal = preg_replace('/^\d*_/', '', $k);
          $depute[$kfinal] = $tops[$k]['value'];
          if (!isset($this->champs[$kfinal])) $this->champs[$kfinal] = 1;
          if ($fin) {
            $depute[$kfinal.'_moyenne_mensuelle']  = $tops[$k]['moyenne'];
            if (!isset($this->champs[$kfinal.'_moyenne_mensuelle'])) $this->champs[$kfinal.'_moyenne_mensuelle'] = 1;
          }
        } else {
          $depute[$k] = $tops[$k];
          if (!isset($this->champs[$k])) $this->champs[$k] = 1;
        }
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
    $colormap = myTools::getGroupesColorMap();
    $groupesorder = myTools::getAllGroupesOrder();
    foreach($orgas as $o) {
      $orga = array();
      $orga['id'] = $o->id * 1;
      $orga['slug'] = $o->slug;
      $orga['nom'] = $o->nom;
      if ($o->type == "groupe") {
        $orga['acronyme'] = $o->getSmallNomGroupe();
        $orga['couleur'] = (isset($colormap[$orga['acronyme']]) ? $colormap[$orga['acronyme']] : '');
        $orga['order'] = (isset($groupesorder[$orga['acronyme']]) ? $groupesorder[$orga['acronyme']] : '');
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
      $this->multi['email'] = 1;
      $this->multi['adresse'] = 1;
      $this->multi['mandat'] = 1;
    }
    $this->multi['site'] = 1;
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
      $depute = $this->getParlementaireArray($dep, $request->getParameter('format'), ($request->getParameter('current') == true || !$orga ? 1 : 2));
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
    $this->res['depute'] = $this->getParlementaireArray($depute, $request->getParameter('format'));
    $this->multi = array();
    $this->multi['responsabilite'] = 1;
    $this->multi['email'] = 1;
    $this->multi['adresse'] = 1;
    $this->multi['mandat'] = 1;
    $this->multi['site'] = 1;
    $this->champ = 'depute';
    $this->breakline = '';
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
    else if ($format == 'csv' && $light != 1)
      $res['mandat_fin'] = "";
    if ($parl->fin_mandat && $parl->fin_mandat >= $parl->debut_mandat)
      $res['ancien_depute'] = 1;
    else if ($format == 'csv' && $light != 1)
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

  public function executeAmendements(sfWebRequest $request) {
    chdir(sfConfig::get('sf_root_dir'));
    $this->task = new printDumpAmendementsLoiTask($this->dispatcher, new sfFormatter());
    $this->loi = preg_replace('/[^0-9a-z\-]/', '', $request->getParameter('loi'));
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
    $this->breakline = '';
  }

  private function addLink($parl_amdt, $parl, $weight) {
    $lid = $parl_amdt['uniq_key'].'-'.min($parl_amdt['parlementaire_id'], $parl).'-'.max($parl_amdt['parlementaire_id'], $parl);
    if (!isset($this->links[$lid]))
      $this->links[$lid] = array('1' => $parl_amdt['parlementaire_id'], '2' => $parl, 'w' => $weight);
    else $this->links[$lid]['w'] += $weight;
  }

}

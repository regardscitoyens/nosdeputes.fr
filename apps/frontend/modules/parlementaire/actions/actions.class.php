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

  public function executeIndex(sfWebRequest $request) {
    $request->setParameter('rss', array(array('link' => '@commentaires_rss', 'title'=>'Les derniers commentaires sur NosDéputés.fr')));
  }

  public function executeFaq(sfWebRequest $request) {
    myTools::setPageTitle("Questions fréquemment posées", $this->response);
  }

  public function executeAssister(sfWebRequest $request) {
    myTools::setPageTitle("Assister aux débats publics de l'Assemblée nationale", $this->response);
  }

  public static function imagetograyscale($im)
  {
    if (imageistruecolor($im)) {
      imagetruecolortopalette($im, false, 256);
    }

    for ($c = 0; $c < imagecolorstotal($im); $c++) {
      $col = imagecolorsforindex($im, $c);
      $gray = round(0.299 * $col['red'] + 0.587 * $col['green'] + 0.114 * $col['blue']);
      imagecolorset($im, $c, $gray, $gray, $gray);
    }
  }

  public static function horizontalFlip(&$img) {
    $size_x = imagesx($img);
    $size_y = imagesy($img);
    $temp = imagecreatetruecolor($size_x, $size_y);
    $x = imagecopyresampled($temp, $img, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
    if ($x) {
      $img = $temp;
    }
    else {
      die("Unable to flip image");
    }
  }

  public function executePhoto(sfWebRequest $request)
  {
    $rayon = 50; //pour la vignette
    $bordure = 10;
    $work_height = 500; //pour éviter des sentiments d'antialiasing

    $slug = $request->getParameter('slug');
    $parlementaire = Doctrine_Query::create()->from('Parlementaire P')->where('slug = ?', $slug)->fetchOne();
    $this->forward404Unless($parlementaire);
    $file = tempnam(sys_get_temp_dir(), 'Parl');
    $photo = $parlementaire->photo;
    if (!strlen($photo)) {
      copy(sfConfig::get('sf_root_dir').'/web/images/xneth/avatar_depute.jpg', $file);
    } else {
      $fh = fopen($file, 'w');
      fwrite($fh ,$photo);
      fclose($fh);
    }
    list($width, $height, $image_type) = getimagesize($file);
    if (!$width || !$height) {
      copy(sfConfig::get('sf_root_dir').'/web/images/xneth/avatar_depute.jpg', $file);
      list($width, $height, $image_type) = getimagesize($file);
    }

    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $newheight = ceil($request->getParameter('height', $height)/10)*10;
    if ($newheight > 250)
      $newheight = 250;
    $ratio = 125/160.;
    $width2 = $width; $height2 = $height;
    if ($ratio > $width/$height)
      $height2 = $width/$ratio;
    else $width2 = $height*$ratio;
    $iorig = imagecreatefromjpeg($file);
    $ih = imagecreatetruecolor($work_height*$ratio, $work_height);
    if (!$request->getParameter('color') && ((!$parlementaire->isEnMandat() && !myTools::isFinlegislature()) || preg_match('/décè/i', $parlementaire->getAnciensMandats())))
      self::imagetograyscale($iorig);
    imagecopyresampled($ih, $iorig, 0, 0, max(0, ($width - $width2)/2), max(0, ($height - $height2)/2), $work_height*$ratio, $work_height, $width2, $height2);
    $width = $work_height*$ratio;
    $height = $work_height;
    imagedestroy($iorig);
    unlink($file);

    if ((isset($parlementaire->autoflip) && $parlementaire->autoflip) XOR $request->getParameter('flip')) {
      self::horizontalFlip($ih);
    }

    $groupe = $parlementaire->groupe_acronyme;
  if ($groupe) {
      imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon+$bordure, $rayon+$bordure, imagecolorallocate($ih, 255, 255, 255));

      $colormap = myTools::getGroupesColorMap();
      if (isset($colormap[$groupe]) && preg_match('/^(\d+),(\d+),(\d+)$/', $colormap[$groupe], $match))
        imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, $match[1], $match[2], $match[3]));

/*  Old code to handle groupes bicolore
	imagefilledarc($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, 45, 225, imagecolorallocate($ih, 0, 170, 0), IMG_ARC_EDGED);
	imagefilledarc($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, 225, 45, imagecolorallocate($ih, 240, 0, 0), IMG_ARC_EDGED);
*/
    }

    if ($newheight) {
      $newwidth = $newheight*$width/$height;
      $image = imagecreatetruecolor($newwidth, $newheight);
      imagecopyresampled($image, $ih, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
      imagedestroy($ih);
      $ih = $image;
    }
    $this->image = $ih;
    $this->getResponse()->addCacheControlHttpHeader('max-age='.(60*60*24*3).',public');
    $this->getResponse()->setHttpHeader('Expires', $this->getResponse()->getDate(time()+60*60*24*3));
  }

  public function executeRandom(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    if (!myTools::isLegislatureCloturee()) {
      $query->where('fin_mandat IS NULL OR debut_mandat > fin_mandat');
    }
    $p = $query->orderBy('rand()')->limit(1)->fetchOne();
    return $this->redirect('@parlementaire?slug='.$p['slug']);
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $request->setParameter('rss', array(array('link' => '@parlementaire_rss?slug='.$this->parlementaire->slug, 'title'=>'L\'activité de '.$this->parlementaire->nom),
					array('link' => '@parlementaire_rss_commentaires?slug='.$this->parlementaire->slug, 'title'=>'Les derniers commentaires portant sur l\'activité de '.$this->parlementaire->nom)
					));
    $this->response->addMeta('keywords', $this->parlementaire->nom.' '.$this->parlementaire->nom_circo.' '.$this->parlementaire->type.' '.$this->parlementaire->groupe_acronyme.' Assemblée nationale');
    $this->response->addMeta('description', 'Pour tout connaître de l\'activité de '.$this->parlementaire->nom.' à l\'Assemblée Nationale. '.$this->parlementaire->nom.' est '.$this->parlementaire->getLongStatut().' à l\'Assemblée Nationale.');
    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');
    $this->response->addMeta('parlementaire_id', 'd'.$this->parlementaire->id);
    $this->response->addMeta('parlementaire_id_url', str_replace('http://', myTools::getProtocol().'://', sfconfig::get('app_base_url')).'id/'.'d'.$this->parlementaire->id);
    $this->response->addMeta('twitter:card', 'summary_large_image');
    $this->response->addMeta('twitter:description', "Retrouvez l'activité parlementaire de ".$this->parlementaire->nom.' à l\'Assemblée nationale');
    $this->response->addMeta('twitter:image', str_replace('http://', myTools::getProtocol().'://', sfconfig::get('app_base_url')).$this->parlementaire->slug.'/preview');

    $this->commission_permanente = null;
    $this->main_fonction = null;
    $this->missions = array();
    foreach ($this->parlementaire->getResponsabilites() as $resp) {
      $permas = myTools::getCommissionsPermanentes();
      if (in_array($resp->Organisme->slug, $permas))
        $this->commission_permanente = $resp;
      else array_push($this->missions, $resp);
      if (preg_match('/^Bureau/', $resp->nom) && preg_match('/(président|questeur)e?$/i', $resp->fonction))
        $this->main_fonction = ucfirst($resp->fonction);
    }

    $this->anciens_mandats = array();
    foreach (unserialize($this->parlementaire->getAnciensMandats()) as $m)
      if (preg_match("/^(.*) \/ (.*) \/ (.*)$/", $m, $match)) {
        if ($match[2] != "")
          $this->anciens_mandats[] = ucfirst($this->parlementaire->getParlFonction())." du $match[1] au $match[2] ($match[3])";
      }
    rsort($this->anciens_mandats);
  }

  public function executePreview(sfWebRequest $request) {
    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);

    $url = $request->getParameter('url');
    if ($url2 = $request->getParameter('url2'))
      $url .= "/$url2";
    if ($url3 = $request->getParameter('url3'))
      $url .= "/$url3";
    if ($url4 = $request->getParameter('url4'))
      $url .= "/$url4";
    if ($url5 = $request->getParameter('url5'))
      $url .= "/$url5";
    $this->url  = sfConfig::get('app_manet_url') .'?url='. urlencode(str_replace('http://', myTools::getProtocol().'://', sfconfig::get('app_base_url')).$url);
    $this->url .= "&format=jpg&clipRect=".urlencode("0,0,1060,555");
  }

  public function executeId(sfWebRequest $request)
  {
    $format = $request->getParameter('format');
    if ($format)
	$format = '/'.$format;
    $id = $request->getParameter('id');
    if (preg_match('/^s/', $id)) $this->redirect(myTools::getProtocol()."://www.nossenateurs.fr/id/$id".$format);
    $id = preg_replace('/^d/', '', $id);
    $p = Doctrine::getTable('Parlementaire')->find($id);
    $this->forward404Unless($p);
    if ($format = $request->getParameter('format')) {
      return $this->redirect('api/parlementaire?format='.$format.'&slug='.$p->slug.'&textplain='.$request->getParameter('textplain'));
    }
    return $this->redirect('@parlementaire?slug='.$p->slug);
  }

  public function executeList(sfWebRequest $request) {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $query->orderBy('p.nom_de_famille ASC');
    $results = $query->execute();
    $this->parlementaires = array();
    foreach ($results as $depute) {
      $lettre = $depute->nom_de_famille[0];
      $lettre = preg_replace('/[ÉÉ]/', 'E', $lettre);
      if (isset($this->parlementaires[$lettre])) $this->parlementaires[$lettre][] = $depute;
      else $this->parlementaires[$lettre] = array($depute);
    }
    unset($results);
    $this->total = Doctrine_Query::create()
      ->from('Parlementaire p')
      ->select('count(distinct p.id) as ct')
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    $this->actifs = Doctrine_Query::create()
      ->from('Parlementaire p')
      ->select('count(distinct p.id) as ct')
      ->where('p.fin_mandat IS NULL')
      ->orWhere('p.fin_mandat < p.debut_mandat')
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    myTools::setPageTitle("Liste de tous les députés à l'Assemblée nationale", $this->response);
  }

  public function executeListProfession(sfWebRequest $request) {
    $this->exact = 0;
    $this->prof = strip_tags(strtolower($request->getParameter('search')));
    if ($this->prof == "") {
      $this->parlementaires = array();
      $this->citoyens = array();
    }
    else {
      $query = Doctrine::getTable('Parlementaire')->createQuery('p')
        ->addSelect('p.fin_mandat')
        ->where('p.profession LIKE ?', $this->prof)
        ->orderBy('p.nom_de_famille ASC');
      $this->parlementaires = $query->execute();
      if (count($this->parlementaires) > 0)
        $this->exact = 1;
      else {
        $query = Doctrine::getTable('Parlementaire')->createQuery('p')
          ->addSelect('p.fin_mandat')
          ->where('p.profession LIKE ?', '%'.$this->prof.'%')
          ->orderBy('p.profession ASC')
          ->addOrderBy('p.nom_de_famille ASC');
        $this->parlementaires = $query->execute();
      }
      $query = Doctrine::getTable('Citoyen')->createQuery('c')
        ->where('c.is_active = true')
        ->andWhere('c.activite LIKE ?', '%'.$this->prof.'%')
        ->orderBy('c.activite ASC')
        ->addOrderBy('c.login');
      $this->citoyens = $query->execute();
    }
    myTools::setPageTitle('Liste de tous les députés "'.$this->prof.'"', $this->response);
  }

  public function executeListGroupe(sfWebRequest $request) {
    $acro = strtolower($request->getParameter('acro'));
    $nom = Organisme::getNomByAcro($acro);
    $this->forward404Unless($nom);

    $query = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->select('p.*, po.fonction as fonction, po.importance as imp, po.debut_fonction as debut_fonction, po.fin_fonction as fin_fonction')
      ->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->where('p.fin_mandat IS NULL OR p.fin_mandat < p.debut_mandat')
      ->andWhere('o.type = ?', 'groupe')
      ->andWhere('o.nom = ?', $nom)
      ->andWhere('(po.fin_fonction IS NULL OR DATE_SUB(po.fin_fonction, INTERVAL 10 DAY) >= po.debut_fonction)')
      ->orderBy('po.fin_fonction, imp DESC, p.nom_de_famille ASC');
    $this->parlementaires = array();
    $this->total = 0;
    foreach ($query->execute() as $depute) {
      $imp = $depute->imp;
      if ($depute->fin_fonction)
        $imp -= 100;
      else $this->total++;
      if (isset($this->parlementaires[$imp])) $this->parlementaires[$imp][] = $depute;
      else $this->parlementaires[$imp] = array($depute);
    }
    $query = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->select('p.*, po.fonction as old_fonction')
      ->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->where('o.nom = ?', $nom)
      ->andWhere('p.fin_mandat IS NOT NULL')
      ->andWhere('p.fin_mandat >= p.debut_mandat')
      ->andWhere('(po.fin_fonction IS NULL OR DATE_SUB(po.fin_fonction, INTERVAL 10 DAY) >= po.debut_fonction)')
      ->orderBy('po.fin_fonction, po.importance DESC, p.nom_de_famille ASC');
    foreach ($query->execute() as $depute) {
      if (isset($this->parlementaires[-200])) $this->parlementaires[-200][] = $depute;
      else $this->parlementaires[-200] = array($depute);
    }
    $query2 = Doctrine::getTable('Organisme')->createQuery('o');
    $query2->where('o.nom = ?', $nom);
    $this->orga = $query2->fetchOne();
    $this->forward404Unless($this->orga);
    $this->title = $this->orga->getNom()." (".$this->orga->getSmallNomGroupe().")";
    myTools::setPageTitle("Liste des députés du groupe ".$this->title, $this->response);
  }

  private function loadOrganismes() {
    $this->organisme_types = array (
      'groupe' => 'Groupes politiques',
      'parlementaire' => 'Fonctions parlementaires (commissions, délégations, missions...)',
      'extra' => 'Missions extra-parlementaires',
      'groupes' => "Groupes d'études et d'amitié"
    );
  }

  public function executeListOrganismes(sfWebRequest $request) {
    $this->loadOrganismes();
    $this->title = "Liste des différents types d'organismes";
    myTools::setPageTitle($this->title, $this->response);
  }

  public function executeListOrganismesType(sfWebRequest $request) {
    $this->type = $request->getParameter('type');
    $this->forward404Unless($this->type);
    $this->loadOrganismes();
    $this->forward404Unless($this->organisme_types[$this->type]);
    $this->organismes = Doctrine_Query::create()
      ->select('o.nom, o.slug, count(distinct p.id) as membres, count(distinct s.id) as reunions')
      ->from('Organisme o')
      ->leftJoin('o.ParlementaireOrganismes po, po.Parlementaire p, o.Seances s')
      ->where('o.type = ?', $this->type)
      ->andWhere('p.fin_mandat IS NULL')
      ->andWhere('po.fin_fonction IS NULL')
      ->groupBy('o.id')
      ->fetchArray();
    $this->human_type = $this->organisme_types[$this->type];
    $this->title = "Liste des ".$this->human_type;
    myTools::setPageTitle($this->title, $this->response);
  }

  public function executeListOrganisme(sfWebRequest $request) {
    $orga = $request->getParameter('slug');
    $this->forward404Unless($orga);
    if (myTools::isLegislatureCloturee())
      $this->response->addMeta('robots', 'noindex,follow');
    $this->orga = Doctrine::getTable('Organisme')->createQuery('o')
      ->where('o.slug = ?', $orga)->fetchOne();
    $this->forward404Unless($this->orga);

    $this->loadOrganismes();
    $this->human_type = $this->organisme_types[$this->orga->type];

    $acro = $this->orga->getSmallNomGroupe();
    if ($acro)
      return $this->redirect('@list_parlementaires_groupe?acro='.$acro);

    $pageS = $request->getParameter('pages', 1);
    $pageR = $request->getParameter('page', 1);
    if ($pageS == 1) {
      if ($pageR == 1)
        $this->page = "home";
      else $this->page = "rapports";
    } else $this->page = "seances";
    if ($this->page === "home") {
      $query = Doctrine::getTable('Parlementaire')->createQuery('p')
        ->select('p.*, po.fonction as fonction, po.importance as imp, po.debut_fonction as debut_fonction, po.fin_fonction as fin_fonction')
        ->leftJoin('p.ParlementaireOrganisme po')
        ->leftJoin('po.Organisme o')
        ->where('o.slug = ?', $orga)
        ->andWhere('(p.fin_mandat IS NULL OR p.fin_mandat < p.debut_mandat)')
        ->andWhere('(po.fin_fonction IS NULL OR DATE_SUB(po.fin_fonction, INTERVAL 10 DAY) >= po.debut_fonction)')
        ->orderBy("po.fin_fonction, po.importance DESC, p.nom_de_famille ASC");
      $this->parlementaires = array();
      $this->total = 0;
      foreach ($query->execute() as $depute) {
        $imp = $depute->imp;
        if ($depute->fin_fonction)
          $imp -= 100;
        else if (!preg_match('/[âa]ge$/i', $depute->fonction)) $this->total++;
        if (isset($this->parlementaires[$imp])) $this->parlementaires[$imp][] = $depute;
        else $this->parlementaires[$imp] = array($depute);
      }
      $query = Doctrine::getTable('Parlementaire')->createQuery('p')
        ->select('p.*, po.fonction as old_fonction')
        ->leftJoin('p.ParlementaireOrganisme po')
        ->leftJoin('po.Organisme o')
        ->where('o.slug = ?', $orga)
        ->andWhere('p.fin_mandat IS NOT NULL')
        ->andWhere('p.fin_mandat >= p.debut_mandat')
        ->andWhere('(po.fin_fonction IS NULL OR DATE_SUB(po.fin_fonction, INTERVAL 10 DAY) >= po.debut_fonction)')
        ->orderBy('po.fin_fonction, po.importance DESC, p.nom_de_famille ASC');
      foreach ($query->execute() as $depute) {
        if (isset($this->parlementaires[-200])) $this->parlementaires[-200][] = $depute;
        else $this->parlementaires[-200] = array($depute);
      }
    }
    if ($this->page === "home" || $this->page === "seances") {
      $query2 = Doctrine::getTable('Seance')->createQuery('s')
        ->leftJoin('s.Organisme o')
        ->where('o.slug = ?', $orga)
        ->orderBy('s.date DESC, s.moment ASC');
      $this->pagerSeances = Doctrine::getTable('Seance')->getPager($request, $query2);
    }
    if ($this->page === "home" || $this->page === "rapports") {
      $query3 =  Doctrine::getTable('Texteloi')->createQuery('t')
        ->leftJoin('t.Organisme o')
        ->where('o.slug = ?', $orga)
        ->orderBy('t.numero DESC, t.annexe ASC');
      $this->pagerRapports = new sfDoctrinePager('Texteloi',10);
      $this->pagerRapports->setQuery($query3);
      $this->pagerRapports->setPage($pageR);
      $this->pagerRapports->init();
    }
    if ($this->orga->type == 'extra')
      $this->detailed_type = 'organisme extra-parlementaire composé';
    else if ($this->orga->type == 'groupes')
      $this->detailed_type = 'groupe composé';
    else $this->detailed_type = (preg_match('/commission/i', $this->orga->getNom()) ? 'comm' : 'm').'ission parlementaire composée';
    $this->detailed_type .= ' de '.$this->total.' député'.($this->total > 1 ? 's' : '');
    $this->title = $this->orga->getNom();
    myTools::setPageTitle($this->title." (".$this->detailed_type.")", $this->response);
  }

  public function executeTag(sfWebRequest $request) {
    $this->tquery = null;
    if ($this->tag = $request->getParameter('tags')) {
      $tags = preg_split('/,/', $this->tag);

      $this->parlementaires = Doctrine::getTable('Intervention')
        ->createQuery('i')
        ->select('i.id, p.*, count(i.id) as nb')
        ->addFrom('i.Parlementaire p, Tagging tg, Tag t')
        ->where('p.id IS NOT NULL')
        ->andWhere('tg.taggable_id = i.id AND t.id = tg.tag_id')
        ->andWhere('tg.taggable_model = ?', 'Intervention')
        ->andWhereIn('t.name', $tags)
        ->groupBy('p.id')
        ->orderBy('nb DESC')
        ->fetchArray();
    }
    myTools::setPageTitle("Les mots-clés des débats de l'Assemblée nationale", $this->response);
  }

  public function executePlot(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->session = $request->getParameter('time');
    $this->forward404Unless(preg_match('/^(legislature|lastyear|2\d{3}2\d{3})$/', $this->session));
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($this->parlementaire);

    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    $this->sessions = Doctrine_Query::create()
      ->select('s.session')
      ->from('Seance s')
      ->leftJoin('s.Presences p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('s.session IS NOT NULL AND s.session <> ""')
      ->groupBy('s.session')->fetchArray();
    $this->title = "Graphes d'activité parlementaire";
    $this->fin = myTools::isFinLegislature();
    if ($this->fin)
      $this->check = 'legislature';
    else {
      $this->check = 'lastyear';
      $mois = min(12, floor((time() - strtotime($this->parlementaire->debut_mandat) ) / (60*60*24*30)));
      $this->mois = ($mois < 2 ? " premier" : "s $mois ".($mois < 12 ? "prem" : "dern")."iers");
    }
    if ($this->session == $this->check) {
      if ($this->fin) $title = 'Sur toute la législature';
      else $title = "Sur le".$this->mois." mois";
    } else $title = 'Sur la session '.preg_replace('/^(\d{4})/', '\\1-', $this->session);
    myTools::setPageTitle($this->title.' de '.$this->parlementaire->nom.' '.strtolower($title), $this->response);
  }

  public static function topSort($a, $b) {
    if ($b[$_GET['sort']]['rank'] == $a[$_GET['sort']]['rank'])
      return strcmp($a[0]['nom_de_famille'], $b[0]['nom_de_famille']);
    else return $a[$_GET['sort']]['rank'] - $b[$_GET['sort']]['rank'];
  }

  public function executeTop(sfWebRequest $request)
  {
    $fin = myTools::isFinLegislature();
    $parlementaires = Doctrine::getTable("Parlementaire")->prepareParlementairesTopQuery($fin)->fetchArray();

    // Prepare les metas des groupes
    $this->gpes = array();
    foreach(myTools::getGroupesInfos() as $gpe) {
      $this->gpes[$gpe[1]] = array();
      $this->gpes[$gpe[1]][0] = array();
      $this->gpes[$gpe[1]][0]['nb'] = 0;
      $this->gpes[$gpe[1]][0]['nom'] = $gpe[0];
      $this->gpes[$gpe[1]][0]['desc'] = $gpe[3];
    }

    $this->tops = array();
    foreach($parlementaires as $p) {
      $tops = unserialize($p['top']);

      // En mode bilan final on n'affiche que les députés avec plus de 6 mois de mandat
      if ($fin && $tops['nb_mois'] < 6)
        continue;

      $parl = array();
      $idx = 0;
      $parl[$idx++] = $p;

      // ajout du nombre de mois de mandat en mode bilan
      if ($fin)
        $parl[0]["nb_mois"] = $tops['nb_mois'];

      // Somme des députés par groupe
      if ($p['groupe_acronyme'] && isset($this->gpes[$p['groupe_acronyme']]))
        $this->gpes[$p['groupe_acronyme']][0]['nb']++;

      // Traite chaque indicateur
      foreach(array_keys($tops) as $k) {
        if ($k == "nb_mois")
          continue;

        $indic = array();
        $indic['rank'] = $tops[$k]['rank'];
        $indic['value'] = $tops[$k]['value'];

        // Style les mieux et moins bien classés
        $indic['style'] = ' ';
        if ($tops[$k]['rank'] < 151 && $tops[$k]['value'])
          $indic['style'] .= 'style="color:green; font-weight:bold;" ';
        else if ($tops[$k]['rank'] > 577 - 151)
          $indic['style'] = ' style="color:red; font-style:italic;" ';

        // Valeur moyenne en mode bilan
        if ($fin)
          $indic['moyenne'] = $tops[$k]['moyenne'];

        // Somme les valeurs par groupe (actuel)
        if ($p['groupe_acronyme'] && isset($this->gpes[$p['groupe_acronyme']])) {
          if (!isset($this->gpes[$p['groupe_acronyme']][$idx]))
            $this->gpes[$p['groupe_acronyme']][$idx] = 0;
          $this->gpes[$p['groupe_acronyme']][$idx] += $tops[$k]['value'];
        }

        $parl[$idx++] = $indic;
      }
      $this->tops[$p['id']] = $parl;
    }

    // Construit la liste des indicateurs du tableau
    $this->ktop = array_keys($tops);
    if ($this->ktop[0] == "nb_mois")
      array_shift($this->ktop);

    // Ordonne par le champ requis si nécessaire
    $this->sort = $this->getRequestParameter('sort');
    if (($_GET['sort'] = $this->sort)) {
      usort($this->tops, 'parlementaireActions::topSort');
    }

    $this->fin = myTools::isFinLegislature();
    $this->fresh = myTools::isFreshLegislature();
    if ($this->fresh) $this->subtitle = "depuis le début de la législature";     else if ($this->fin) $this->subtitle = "sur toute la législature";
    else $this->subtitle = "sur les 12 derniers mois";
    myTools::setPageTitle("Synthèse générale de l'activité parlementaire des députés ".$this->subtitle, $this->response);
  }

  public static function dateSort($a, $b) {
    $datea = $a->updated_at;
    $dateb = $b->updated_at;
    if (get_class($a) === 'Texteloi')
      $datea = $a->date;
    if (get_class($b) === 'Texteloi')
      $datea = $b->date;
    return str_replace('-', '', $dateb) - str_replace('-', '', $datea);
  }
  public function executeRss(sfWebRequest $request) {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);


    if (!$request->getParameter('Document')) {
      $request->setParameter('query', 'tag:"Parlementaire='.$this->parlementaire.'"');
      $request->setParameter('title', preg_replace('/%/', $this->parlementaire->nom, $request->getParameter('title')));

      if ($o = $request->getParameter('object_type'))
	$request->setParameter('query', $request->getParameter('query').' object_type='.$o);
      $request->setParameter('format', 'rss');
      return $this->forward('solr', 'search');
    }

    $this->limit = 30;

    $news = array();
    $elements = 0;
    if ($request->getParameter('Intervention')) {
      $elements++;
      foreach(Doctrine::getTable('Intervention')->createQuery('i')
		->where('i.parlementaire_id = ?', $this->parlementaire->id)
		->limit($this->limit)->orderBy('updated_at DESC')->execute()
		as $n)
        $news[] = $n;
    }
    if ($request->getParameter('QuestionEcrite')) {
      $elements++;
      foreach(Doctrine::getTable('QuestionEcrite')->createQuery('q')
	      ->where('q.parlementaire_id = ?', $this->parlementaire->id)
	      ->limit($this->limit)->orderBy('updated_at DESC')->execute()
	      as $n)
	$news[] = $n;
    }
    if ($request->getParameter('Amendement')) {
      $elements++;
      foreach(Doctrine::getTable('Amendement')->createQuery('a')
	      ->leftJoin('a.ParlementaireAmendement pa')
	      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
              ->andWhere('a.sort <> ?', 'Rectifié')
              ->orderBy('updated_at DESC')->limit($this->limit)->execute()
	      as $n)
	$news[] = $n;
    }
    if ($request->getParameter('Document')) {
      $elements++;
      $docquery = Doctrine::getTable('Texteloi')->createQuery('t')
        ->leftJoin('t.ParlementaireTexteloi pt')
        ->where('pt.parlementaire_id = ?', $this->parlementaire->id);
      $type = $request->getParameter('type');
      if ($type) {
        $lois = array('Proposition de loi', 'Proposition de résolution');
        if ($type === "loi")
          $docquery->andWhere('t.type = ? OR t.type = ?', $lois);
        else if ($type === "rap")
          $docquery->andWhere('t.type != ? AND t.type != ?', $lois);
      }
      foreach($docquery->orderBy('date DESC')->limit($this->limit)->execute()
              as $n)
        $news[] = $n;
    }

    if ($elements > 1) usort($news, 'parlementaireActions::dateSort');

    $this->news = $news;
    $this->feed = new sfRssFeed();
  }

  public function executeError404(sfWebRequest $request) {
    $prevHost = myTools::getPreviousHost();
    $uri = $_SERVER["REQUEST_URI"];
    if ($prevHost &&
      (preg_match('/^\/(api|1\d)\//', $uri) || preg_match('/^\/[^\/]+$/', $uri) || preg_match('/^\/depute\/photo\//', $uri)) &&
      !preg_match('/^\/'.myTools::getLegislature().'\//', $uri)
    ) {
      $this->response->setHttpHeader('Location', myTools::getProtocol()."://".$prevHost.$uri);
      print myTools::getProtocol()."://".$prevHost.$uri;
    } elseif (preg_match('#/(xml|json|csv)(\?.*)?$#', $uri, $match)) {
      $this->setLayout(false);
      $this->setTemplate($match[1], "api");
      $this->response->setStatusCode(200);
    } else {
      $this->response->setHttpHeader('Status', '404 Not found');
      $this->response->setTitle("Erreur 404 - Page introuvable - NosDéputés.fr");
    }
  }


  private function searchDepute($search) {
    $sexe = null;
    if (preg_match("/M\([.mle]\)+ */", $search, $match)) {
      $sexe = "H";
      if (preg_match("/e/", $match[1]))
        $sexe = "F";
      $search = preg_replace("/^.*M\([.mle]\)+ */", "", $search);
    }
    $search = preg_replace("/([ \-.]\w)/", strtoupper("\\1"), ucfirst(strtolower($search)));
    $dep = Doctrine::getTable('Parlementaire')->findOneBySlug(strtolower($search));
    if (!$dep)
      $dep = Doctrine::getTable('Parlementaire')->findOneByNom($search);
    if (!$dep)
      $dep = Doctrine::getTable('Parlementaire')->findOneByNomDeFamille($search);
#   if (!$dep)
#     $dep = Doctrine::getTable('Parlementaire')->findOneByNomSexeGroupeCirco($search, $sexe);
      return $dep;
  }

  public function executeWidgetEditor(sfWebRequest $request) {
    $this->depute = $this->searchDepute($request->getParameter('depute'));
    if (!$this->depute)
    $this->depute = Doctrine::getTable('Parlementaire')->createQuery('p')->where('fin_mandat IS NULL OR debut_mandat > fin_mandat')->orderBy('rand()')->limit(1)->fetchOne();
    $this->title = "Afficher des extraits de NosDéputés.fr sur votre site";
    myTools::setPageTitle($this->title, $this->response);
  }

  public function executeWidget(sfWebRequest $request) {
    $this->setLayout(false);
    $this->search = $request->getParameter('depute');
    $this->internal = $request->getParameter('internal');
    $dep = $this->searchDepute($this->search);
    $this->parl = null;
    if (!$dep) return;
    $this->parl = $dep->slug;
    if ($this->parl != $this->search && !$this->internal) {
      return $this->redirect('parlementaire/widget?depute='.$this->parl."&".$_SERVER['QUERY_STRING']);
    }
    $this->options = array('titre' => 1, 'photo' => 1, 'graphe' => 1, 'activite' => 1, 'tags' => 1, 'iframe' => 0);
    if ($request->getParameter('notitre', false))
      $this->options['titre'] = 0;
    if ($request->getParameter('nophoto', false))
      $this->options['photo'] = 0;
    if ($request->getParameter('nographe', false))
      $this->options['graphe'] = 0;
    if ($request->getParameter('noactivite', false))
      $this->options['activite'] = 0;
    if ($request->getParameter('notags', false))
      $this->options['tags'] = 0;
    if (preg_match('/^\d+$/', $request->getParameter('maxtags', 40)))
      $this->options['maxtags'] = $request->getParameter('maxtags', 40);
    if (preg_match('/^\d+$/', $request->getParameter('width', 935)))
      $this->options['width'] = $request->getParameter('width', 935);
    if ($request->getParameter('iframe', false))
      $this->options['iframe'] = 1;
  }
}

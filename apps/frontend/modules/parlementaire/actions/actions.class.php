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
  public function executeFaq(sfWebRequest $request) {
    $this->response->setTitle('Questions fréquemment posées - NosSénateurs.fr');
  }

  public function executeAssister(sfWebRequest $request) {
    $this->response->setTitle('Assister aux débats publics du Sénat - NosSénateurs.fr');
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
    $ratio = 160/110;

    $slug = $request->getParameter('slug');
    $parlementaire = Doctrine_Query::create()->from('Parlementaire P')->where('slug = ?', $slug)->fetchOne();
    $this->forward404Unless($parlementaire);
    $file = tempnam(sys_get_temp_dir(), 'Parl');
    $photo = $parlementaire->photo;
    if (!strlen($photo)) {
      copy(sfConfig::get('sf_root_dir').'/web/images/xneth/avatar_senateur.jpg', $file);
    } else {
      $fh = fopen($file, 'w');
      fwrite($fh ,$photo);
      fclose($fh);
    }
    list($width, $height, $image_type) = getimagesize($file);
    if (!$width || !$height) {
      copy(sfConfig::get('sf_root_dir').'/web/images/xneth/avatar_senateur.jpg', $file);
      list($width, $height, $image_type) = getimagesize($file);
    }

    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $newheight = ceil($request->getParameter('height', $height)/10)*10;
    if ($newheight > 250)
      $newheight = 250;

    $iorig = imagecreatefromjpeg($file);
    $colortop = imagecolorat($iorig, 1, 1);
    $ih = imagecreatetruecolor($work_height*$width/$height, $work_height);

    if (true || !$request->getParameter('color') && $parlementaire->fin_mandat >= $parlementaire->debut_mandat)
      self::imagetograyscale($iorig);

    imagecopyresampled($ih, $iorig, 0, 0, 0, 0, $work_height*$width/$height, $work_height, $width, $height);
    $width = $work_height*$width/$height;
    $height = $work_height;

   if (abs($height/$width - $ratio) > 0) {
	$iorig = imagecreatetruecolor($width, $width*$ratio);
        imagecopyresampled($iorig, $ih, 0, 0, 0, 0, $width, $width*$ratio, $width, $height);
//	imagefilledrectangle($iorig, 0, - $height + $width*$ratio, $width, $height - $width*$ratio, $colortop);
	$height = $width*$ratio;

        imagedestroy($ih);
        $ih = $iorig;
    }else{
        imagedestroy($iorig);
    }
    unlink($file);

    if ((isset($parlementaire->autoflip) && $parlementaire->autoflip) XOR $request->getParameter('flip')) {
      self::horizontalFlip($ih);
    }

    $groupe = $parlementaire->groupe_acronyme;
    if ($groupe) {
      imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon+$bordure, $rayon+$bordure, imagecolorallocate($ih, 255, 255, 255));
      foreach (myTools::getGroupesInfos() as $gpe)
        if ($gpe[1] == $groupe && preg_match('/^(\d+),(\d+),(\d+)$/', $gpe[2], $match))
         imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, $match[1], $match[2], $match[3]));
    }

    if ($newheight) {
      $newwidth = $newheight/$ratio;
      $image = imagecreatetruecolor($newwidth, $newheight);
      imagecopyresampled($image, $ih, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
      imagedestroy($ih);
      $ih = $image;
    }
    $this->image = $ih;
    $this->getResponse()->addCacheControlHttpHeader('max_age=60');
    $this->getResponse()->setHttpHeader('Expires', $this->getResponse()->getDate(time()*2));
  }

  public function executeIndex(sfWebRequest $request) {
    $request->setParameter('rss', array(array('link' => '@commentaires_rss', 'title'=>'Les derniers commentaires sur NosSénateurs.fr')));
  }

  public function executeRandom(sfWebRequest $request)
  {
    $p = Doctrine::getTable('Parlementaire')->createQuery('p')->where('fin_mandat IS NULL')->orderBy('rand()')->limit(1)->fetchOne();
    return $this->redirect('@parlementaire?slug='.$p['slug']);
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $request->setParameter('rss', array(array('link' => '@parlementaire_rss?slug='.$this->parlementaire->slug, 'title'=>'L\'activité de '.$this->parlementaire->nom),
					array('link' => '@parlementaire_rss_commentaires?slug='.$this->parlementaire->slug, 'title'=>'Les derniers commentaires portant sur l\'activité de '.$this->parlementaire->nom)
					));
    $this->response->addMeta('keywords', $this->parlementaire->nom.' '.$this->parlementaire->nom_circo.' '.$this->parlementaire->type.' '.$this->parlementaire->groupe_acronyme.' Sénat');
    $this->response->addMeta('description', 'Pour tout connaître de l\'activité de '.$this->parlementaire->nom.' au Sénat. '.$this->parlementaire->nom.' est '.$this->parlementaire->getLongStatut().' au Sénat Français.');
    $this->response->addMeta('parlementaire_id', 'd'.$this->parlementaire->id);
    $this->response->addMeta('parlementaire_id_url', myTools::getProtocol().'://www.nossenateurs.fr/id/'.'d'.$this->parlementaire->id);

    $this->commissions_permanentes = array();
    $this->missions = array();

    foreach ($this->parlementaire->getResponsabilites() as $resp) {
      if (in_array($resp->organisme_id, array(2, 330, 9, 20, 27, 371, 738))) {
	array_push($this->commissions_permanentes, $resp);
      }else{
	array_push($this->missions, $resp);
      }
    }
  }

  public function executeId(sfWebRequest $request)
  {
    $format = $request->getParameter('format');
    if ($format)
      $format = '/'.$format;
    $id = $request->getParameter('id');
    if (preg_match('/^d/', $id)) $this->redirect(myTools::getProtocol()."://www.nosdeputes.fr/id/$id".$format);
    $id = preg_replace('/^s/', '', $id);
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
    $this->parlementaires = array();
    foreach ($query->execute() as $senateur) {
      $lettre = ucfirst($senateur->nom_de_famille[0]);
      $lettre = preg_replace('/[ÉÉ]/', 'E', $lettre);
      if (isset($this->parlementaires[$lettre])) $this->parlementaires[$lettre][] = $senateur;
      else $this->parlementaires[$lettre] = array($senateur);
    }
    $ctquery = Doctrine_Query::create()
      ->from('Parlementaire p')
      ->select('count(distinct p.id) as ct')
      ->fetchOne();
    $this->total = $ctquery['ct'];
    $ctquery = Doctrine_Query::create()
      ->from('Parlementaire p')
      ->select('count(distinct p.id) as ct')
      ->where('p.fin_mandat IS NULL')
      ->orWhere('p.fin_mandat < p.debut_mandat')
      ->fetchOne();
    $this->actifs = $ctquery['ct'];
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
  }
  public function executeListGroupe(sfWebRequest $request) {
    $acro = strtolower($request->getParameter('acro'));
    $nom = Organisme::getNomByAcro($acro);
    $this->forward404Unless($nom);
    $query = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->select('p.*, po.fonction as fonction, po.importance as imp')
      ->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->where('p.groupe_acronyme = ?', strtoupper($acro))
      ->andWhere('p.fin_mandat IS NULL')
      ->andWhere('o.type = ?', 'groupe')
      ->andWhere('o.nom = ?', $nom)
      ->orderBy('imp DESC, p.nom_de_famille ASC');
    $this->parlementaires = array();
    $this->total = 0;
    foreach ($query->execute() as $senateur) {
      $this->total++;
      $imp = $senateur->imp;
      if (isset($this->parlementaires[$imp])) $this->parlementaires[$imp][] = $senateur;
      else $this->parlementaires[$imp] = array($senateur);
    }
    $query = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->select('p.*')
      ->where('p.groupe_acronyme = ?', strtoupper($acro))
      ->andWhere('p.fin_mandat IS NOT NULL')
      ->andWhere('p.fin_mandat > p.debut_mandat')
      ->orderBy('p.nom_de_famille ASC');
    foreach ($query->execute() as $senateur) {
      $this->total++;
      if (isset($this->parlementaires[0])) $this->parlementaires[0][] = $senateur;
      else $this->parlementaires[0] = array($senateur);
    }

    $query2 = Doctrine::getTable('Organisme')->createQuery('o');
    $query2->where('o.nom = ?', $nom);
    $this->orga = $query2->fetchOne();
  }

  public function executeListOrganisme(sfWebRequest $request) {
    $orga = $request->getParameter('slug');
    $this->forward404Unless($orga);
    $this->orga = Doctrine::getTable('Organisme')->createQuery('o')
      ->where('o.slug = ?', $orga)->fetchOne();
    $this->forward404Unless($this->orga);

    $pageS = $request->getParameter('pages', 1);
    $pageR = $request->getParameter('page', 1);
    if ($pageS == 1) {
      if ($pageR == 1)
        $this->page = "home";
      else $this->page = "rapports";
    } else $this->page = "seances";
    if ($this->page === "home") {
      $query = Doctrine::getTable('Parlementaire')->createQuery('p')
        ->select('p.*, po.fonction as fonction, po.importance as imp')
        ->leftJoin('p.ParlementaireOrganisme po')
        ->leftJoin('po.Organisme o')
        ->where('o.slug = ?', $orga)
        ->andWhere('p.fin_mandat IS NULL')
	->andWhere('po.fonction NOT LIKE ?', '%ancien %')
        ->orderBy("po.importance DESC, p.nom_de_famille ASC");
      $this->parlementaires = array();
      $this->total = 0;
      foreach ($query->execute() as $senateur) {
        $this->total++;
        $imp = $senateur->imp;
        if (isset($this->parlementaires[$imp])) $this->parlementaires[$imp][] = $senateur;
        else $this->parlementaires[$imp] = array($senateur);
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
  }

  public function executeTag(sfWebRequest $request) {
    $this->tquery = null;
    if ($this->tag = $request->getParameter('tags')) {
      $tags = split(',', $this->tag);

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
    $this->response->setTitle('Les parlementaires par tag - NosSénateurs.fr');
  }

  public function executePlot(sfWebRequest $request) {
    $slug = $request->getParameter('slug');
    $this->session = $request->getParameter('time');
    $this->forward404Unless(preg_match('/^(lastyear|2\d{3}2\d{3})$/', $this->session));
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($this->parlementaire);
    $this->sessions = Doctrine_Query::create()
      ->select('s.session')
      ->from('Seance s')
      ->leftJoin('s.Presences p')
      ->where('p.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('s.session IS NOT NULL AND s.session <> ""')
      ->groupBy('s.session')->fetchArray();
  }

  public static function topSort($a, $b) {
    if ($b[$_GET['sort']]['value'] == $a[$_GET['sort']]['value'])
      return strcmp($a[0]['nom_de_famille'], $b[0]['nom_de_famille']);
    else return $b[$_GET['sort']]['value'] - $a[$_GET['sort']]['value'];
  }

  public function executeTop(sfWebRequest $request) {
    $qp = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->top_link = '@top_global_sorted?';
    if (($o = $request->getParameter('organisme'))) {
      $this->top_link = '@top_organisme_global_sorted?organisme='.$o.'&';
      $organisme = Doctrine::getTable('Organisme')->findOneBySlug($o);
      $this->forward404Unless($organisme);
      $ids = array();
      foreach(Doctrine::getTable('ParlementaireOrganisme')->createQuery('po')
	      ->select('DISTINCT parlementaire_id as id')
	      ->where('organisme_id = ?', $organisme->id)->fetchArray() as $id) {
	$ids[] = $id['id'];
      }
      $qp->whereIn('id', $ids);
    }
    if (myTools::isDebutMandature())
      $enddate = myTools::getDebutMandature();
    else $enddate = date('Y-m-d', time()-round(60*60*24*3650/12));
    $qp->andWhere('fin_mandat IS NULL')
      ->andWhere('debut_mandat < ?', $enddate)
      ->orderBy('nom_de_famille');
    $parlementaires = $qp->fetchArray();
    unset($qp);
    $this->tops = array();
    $this->gpes = array();
    foreach(myTools::getGroupesInfosOrder() as $gpe) {
      $this->gpes[$gpe[1]] = array();
      $this->gpes[$gpe[1]][0] = array();
      $this->gpes[$gpe[1]][0]['nb'] = 0;
      $this->gpes[$gpe[1]][0]['nom'] = $gpe[0];
      $this->gpes[$gpe[1]][0]['desc'] = $gpe[3];
    }
    foreach($parlementaires as $p) {
      $tops = unserialize($p['top']);
      $id = $p['id'];
      $i = 0;
      $this->tops[$id][$i++] = $p;
      $this->gpes[$p['groupe_acronyme']][0]['nb']++;
      foreach(array_keys($tops) as $key) {
	$this->tops[$id][$i]['value'] = $tops[$key]['value'];

	$this->tops[$id][$i]['style'] = '';
	if ($tops[$key]['rank'] <= 100)
	  $this->tops[$id][$i]['style'] = ' style="color:green;font-weight: bold;" ';
	else if ($tops[$key]['rank'] >= count($parlementaires) - 100)
	  $this->tops[$id][$i]['style'] = ' style="color:red;font-style : italic;" ';
        if (!isset($this->gpes[$p['groupe_acronyme']][$i]))
          $this->gpes[$p['groupe_acronyme']][$i] = 0;
        $this->gpes[$p['groupe_acronyme']][$i] += $tops[$key]['value'];
	$i++;
      }
    }
    $this->ktop = array_keys($tops);
    $this->sort = $this->getRequestParameter('sort');
    if (($_GET['sort'] = $this->sort)) {
      usort($this->tops, 'parlementaireActions::topSort');
    }
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

      if ($o = $request->getParameter('object_name'))
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
    if ($request->getParameter('Question')) {
      $elements++;
      foreach(Doctrine::getTable('Question')->createQuery('q')
              ->where('q.parlementaire_id = ?', $this->parlementaire->id)
              ->limit($this->limit)->orderBy('updated_at DESC')->execute()
              as $n)
        $news[] = $n;
    }
    if ($request->getParameter('QuestionEcrite')) {
      $elements++;
      foreach(Doctrine::getTable('Question')->createQuery('q')
	      ->where('q.parlementaire_id = ?', $this->parlementaire->id)
              ->andWhere('q.type = ? ', "Question écrite")
	      ->limit($this->limit)->orderBy('updated_at DESC')->execute()
	      as $n)
	$news[] = $n;
    }
    if ($request->getParameter('QuestionOrale')) {
      $elements++;
      foreach(Doctrine::getTable('Question')->createQuery('q')
              ->where('q.parlementaire_id = ?', $this->parlementaire->id)
              ->andWhere('q.type != ? ', "Question écrite")
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
  public function executeError404() {
  }
}

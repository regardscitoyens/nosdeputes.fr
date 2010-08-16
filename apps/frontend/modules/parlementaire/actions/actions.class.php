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
    $parlementaires = Doctrine_Query::create()->from('Parlementaire P')->where('slug = ?', $slug)->fetchArray();
    $this->forward404Unless($parlementaires[0]);
    $file = tempnam(sys_get_temp_dir(), 'Parl');
    if (!$parlementaires[0]['photo']) {
      copy(sfConfig::get('sf_root_dir').'/web/images/xneth/avatar_depute.jpg', $file); 
    } else {
      $fh = fopen($file, 'w');
      fwrite($fh ,$parlementaires[0]['photo']);
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

    $iorig = imagecreatefromjpeg($file);
    $ih = imagecreatetruecolor($work_height*$width/$height, $work_height);
    if ($parlementaires[0]['fin_mandat'])
      self::imagetograyscale($iorig);
    imagecopyresampled($ih, $iorig, 0, 0, 0, 0, $work_height*$width/$height, $work_height, $width, $height);
    $width = $work_height*$width/$height;
    $height = $work_height;
    imagedestroy($iorig);
    unlink($file);

    if ((isset($parlementaires[0]['autoflip']) && $parlementaires[0]['autoflip']) XOR $request->getParameter('flip')) {
      self::horizontalFlip($ih);
    }

    $groupe = $parlementaires[0]['groupe_acronyme'];
    if ($groupe && !$parlementaires[0]['fin_mandat']) {
      imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon+$bordure, $rayon+$bordure, imagecolorallocate($ih, 255, 255, 255));
      if ($groupe == 'GDR') {
	imagefilledarc($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, 45, 225, imagecolorallocate($ih, 0, 170, 0), IMG_ARC_EDGED);
	imagefilledarc($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, 225, 45, imagecolorallocate($ih, 240, 0, 0), IMG_ARC_EDGED);
      }else if ($groupe == 'SRC') {
	imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, 255, 20, 160));
      }else if ($groupe == 'UMP') {
	imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, 0, 0, 170));
      }else if ($groupe == 'NC') {
	imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, 0, 160, 255));
      }else if ($groupe == 'NI') {
	imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon, $rayon, imagecolorallocate($ih, 255, 255, 255));
      }
    }

    if ($newheight) {
      $newwidth = $newheight*$width/$height;
      $image = imagecreatetruecolor($newwidth, $newheight);
      imagecopyresampled($image, $ih, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
      imagedestroy($ih);
      $ih = $image;
    }
    $this->image = $ih;
    $this->getResponse()->addCacheControlHttpHeader('max_age=60');
    $this->getResponse()->setHttpHeader('Expires', $this->getResponse()->getDate(time()*2));
  }

  public function executeIndex(sfWebRequest $request)
  {
      //      ->where('i.date > ?', date('Y-m-d', time()-60*60*24*31*3));
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
    $this->response->addMeta('keywords', $this->parlementaire->nom.' '.$this->parlementaire->nom_circo.' '.$this->parlementaire->type.' '.$this->parlementaire->groupe_acronyme.' Assemblée nationale');
    $this->response->addMeta('description', 'Pour tout connaître de l\'activité de '.$this->parlementaire->nom.' à l\'Assemblée Nationale. '.$this->parlementaire->nom.' est '.$this->parlementaire->getLongStatut().' à l\'Assemblée Nationale.');
    $this->response->addMeta('parlementaire_id', 'd'.$this->parlementaire->id);
    $this->response->addMeta('parlementaire_id_url', 'http://www.nosdeputes.fr/id/'.'d'.$this->parlementaire->id);

  }

  public function executeId(sfWebRequest $request)
  {
    $id = preg_replace('/^d/', '', $request->getParameter('id'));
    $p = Doctrine::getTable('Parlementaire')->find($id);
    if ($type = $request->getParameter('type')) {
      return $this->redirect('api/parlementaire?type='.$type.'&slug='.$p->slug.'&textplain='.$request->getParameter('textplain'));
    }
    return $this->redirect('@parlementaire?slug='.$p->slug);
  }

  public function executeList(sfWebRequest $request) {
    $this->search = strip_tags($request->getParameter('search'));
    if (!$this->search) $this->search = 'all';
    if (preg_match('/^[A-Z]$/', $this->search))
      return $this->redirect('@list_parlementaires#'.$this->search);
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    if ($this->search != 'all') {
      $searchs = explode(' ', preg_replace('/\W/', ' ', $this->search));
      $ns = count($searchs);
      for ($i=0; $i<$ns; $i++)
        $searchs[$i] = '%'.$searchs[$i].'%';
      $likes = 'p.nom LIKE ?';
      for ($i=1; $i<$ns; $i++)
        $likes .= ' AND p.nom LIKE ?';
      $query->where($likes, $searchs);
    }
    $query->orderBy('p.nom_de_famille ASC');
    if ($this->search == 'all') {
      $this->parlementaires = array();
      foreach ($query->execute() as $depute) {
        $lettre = $depute->nom_de_famille[0];
        if (isset($this->parlementaires[$lettre])) $this->parlementaires[$lettre][] = $depute;
        else $this->parlementaires[$lettre] = array($depute);
      }
    } else $this->parlementaires = $query->execute();
    if (!preg_match('/^([A-ZÉ]|all)$/', $this->search)) {
      $nb = count($this->parlementaires);
      if ($nb == 1) {
        return $this->redirect('@parlementaire?slug='.$this->parlementaires[0]->slug);
      }
      if ($nb == 0) {
        $this->similars = Doctrine::getTable('Parlementaire')->similarTo($this->search, null, 1);
      }
    } else {
      $ctquery = doctrine_query::create()
        ->from('Parlementaire p')
        ->select('count(distinct p.id) as ct')
        ->fetchOne();
      $this->total = $ctquery['ct'];
      $ctquery = doctrine_query::create()
        ->from('Parlementaire p')
        ->select('count(distinct p.id) as ct')
        ->where('p.fin_mandat IS NULL')
        ->orWhere('p.fin_mandat < p.debut_mandat')
        ->fetchOne();
      $this->actifs = $ctquery['ct'];
    }
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
      ->where('p.groupe_acronyme = ?', $acro)
      ->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->where('o.type = ?', 'groupe')
      ->andWhere('o.nom = ?', $nom);
    $query->orderBy("po.importance DESC, p.sexe ASC, p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);

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

    $query = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->where('o.slug = ?', $orga)
      ->orderBy("po.importance DESC, p.sexe ASC, p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);

    $query2 = Doctrine::getTable('Seance')->createQuery('s')
      ->leftJoin('s.Organisme o')
      ->where('o.slug = ?', $orga)
      ->orderBy('s.date DESC, s.moment ASC');
    $this->pagerSeances = Doctrine::getTable('Seance')->getPager($request, $query2);
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
    $this->response->setTitle('Les parlementaires par tag');
  }

  public function executePlot(sfWebRequest $request)
  {
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

  public function executeTop(sfWebRequest $request)
  {
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
    $qp->andWhere('fin_mandat IS NULL')
      ->andWhere('debut_mandat < ?', date('Y-m-d', time()-60*60*24*365/2))
      ->orderBy('nom_de_famille');
    $parlementaires = $qp->fetchArray();
    unset($qp);
    $this->tops = array();
    foreach($parlementaires as $p) {
      //A supprimer lorsque les tops seront unifiés (cf. lib modele parlementaire)
      $tops = unserialize(preg_replace('/s:20:"hemicycle_invectives/', 's:31:"hemicycle_interventions_courtes', $p['top']));
      $id = $p['id'];
      $i = 0;
      $this->tops[$id][$i++] = $p;

      foreach(array_keys($tops) as $key) {
	$this->tops[$id][$i]['value'] = $tops[$key]['value'];

	$this->tops[$id][$i]['style'] = '';
	if ($tops[$key]['rank'] < 151)
	  $this->tops[$id][$i]['style'] = ' style="color:green" ';
	else if ($tops[$key]['rank'] > 577 - 151)
	  $this->tops[$id][$i]['style'] = ' style="color:red" ';
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
    return str_replace('-', '', $b->updated_at) - str_replace('-', '', $a->updated_at);
  }
  public function executeRss(sfWebRequest $request) {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

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
    
    if ($elements > 1) usort($news, 'parlementaireActions::dateSort');

    $this->news = $news;
    $this->feed = new sfRssFeed();
  }
  public function executeError404() {
  }
}

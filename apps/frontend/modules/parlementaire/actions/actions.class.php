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
  public function executePhoto(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $parlementaires = Doctrine_Query::create()->from('Parlementaire P')->where('slug = ?', $slug)->fetchArray();
    $this->forward404Unless($parlementaires[0]);
    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $file = tempnam('/tmp/', 'Parl');
    $fh = fopen($file, 'w');
    fwrite($fh ,$parlementaires[0]['photo']);
    fclose($fh);
    list($width, $height, $image_type) = getimagesize($file);
    $newheight = ceil($request->getParameter('height', $height)/10)*10;

    $iorig = imagecreatefromjpeg($file);
    $ih = imagecreatetruecolor(500*$width/$height, 500);
    imagecopyresampled($ih, $iorig, 0, 0, 0, 0, 500*$width/$height, 500, $width, $height);
    $width = 500*$width/$height;
    $height = 500;
    imagedestroy($iorig);

    unlink($file);


    $rayon = 50;



    $groupe = $parlementaires[0]['groupe_acronyme'];
    if ($groupe) {
      imagefilledellipse($ih, $width-$rayon, $height-$rayon, $rayon+10, $rayon+10, imagecolorallocate($ih, 255, 255, 255));
    }
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

    if ($newheight) {
      $newwidth = $newheight*$width/$height;
      $image = imagecreatetruecolor($newwidth, $newheight);
      imagecopyresampled($image, $ih, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
      imagedestroy($ih);
      $ih = $image;
    }
    $this->image = $ih;
  }

  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $this->forward404Unless($this->parlementaire);
      /*->createQuery('p')
      ->where('p.slug = ?', $slug)
      ->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->fetchOne();*/
    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t, Intervention i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.id = tg.taggable_id');

    $this->textes = doctrine_query::create()
      ->from('Section s')
      ->select('s.section_id, sp.titre, count(i.id) as nb')
      ->where('s.section_id = sp.id')
      ->leftJoin('s.Section sp')
      ->leftJoin('s.Interventions i')
      ->andWhere('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.nb_mots > 20')
      ->groupBy('s.section_id')
      ->orderBy('nb DESC')
      ->fetchArray();
  }

public function executeList(sfWebRequest $request)
  {
    $this->search = $request->getParameter('search');
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    if ($this->search)
      $query->where('p.nom LIKE ?' , '%'.$this->search.'%');
    $query->orderBy("p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
    if ($this->pager->getNbResults() == 1) {
      $p = $this->pager->getResults();
      return $this->redirect('parlementaire/show?slug='.$p[0]->slug);
    }
  }

  public function executeListCirco(sfWebRequest $request) {
    $departmt = trim(strtolower($request->getParameter('search')));
    if (preg_match('/\d+/', $departmt, $match)) {
      $this->num = preg_replace('/^0+/', '', $match[0]);
    } else {
      $dpt = preg_replace('/\s+/','-', $departmt);
      $dpt = preg_replace('/\-st\-/','saint', $dpt);
      $dpt = preg_replace('/(é|è|e)/','e', $dpt);
      $dpt = preg_replace('/à/','a', $dpt);
      $dpt = preg_replace('/ô/','o', $dpt);
      $this->num = Parlementaire::getNumeroDepartement($dpt);
    }
    if (preg_match('/(polynesie|polynésie)/i', $departmt)) {
      $this->circo = "Polynésie Française";
      $this->num = 987;
    } else if ($this->num > 0) {
      $this->circo = Parlementaire::getNomDepartement($this->num);
    } else $this->circo = $departmt;
    if ($departmt == "")
      $this->parlementaires = array();
    else {
      $query = Doctrine::getTable('Parlementaire')->createQuery('p');
      if ($this->num == 0) $query
        ->where('p.nom_circo LIKE ?',  '%'.$this->circo.'%')
        ->orderBy('p.nom_circo');
      else {
        $query->where('p.nom_circo = ?', $this->circo);
        $query2 = Doctrine_Query::create()
          ->select('count(distinct num_circo) as nombre')->from('Parlementaire p')
          ->where('p.nom_circo = ?', $this->circo)->fetchOne();
        $this->n_circo = $query2['nombre'];
      }
      $query->addOrderBy('p.num_circo');
      $this->parlementaires = $query->execute();
    }
  }
  
  public function executeListProfession(sfWebRequest $request) {
    $this->exact = 0;
    $this->prof = strtolower($request->getParameter('search'));
    if ($this->prof == "")
      $this->parlementaires = array();
    else {
      $query = Doctrine::getTable('Parlementaire')->createQuery('p')
        ->addSelect('p.fin_mandat')
        ->where('p.profession LIKE ?', $this->prof)
        ->orderBy("p.nom_de_famille ASC");
      $this->parlementaires = $query->execute();
      if (count($this->parlementaires) > 0)
        $this->exact = 1;
      else {
        $query = Doctrine::getTable('Parlementaire')->createQuery('p')
          ->addSelect('p.fin_mandat')
          ->where('p.profession LIKE ?', '%'.$this->prof.'%')
          ->orderBy("p.profession ASC")
          ->addOrderBy("p.nom_de_famille ASC");
        $this->parlementaires = $query->execute();
      }
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

    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $query->leftJoin('p.ParlementaireOrganisme po')
      ->leftJoin('po.Organisme o')
      ->where('o.slug = ?', $orga);
    $query->orderBy("po.importance DESC, p.sexe ASC, p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);

    $query2 = Doctrine::getTable('Organisme')->createQuery('o');
    $query2->where('o.slug = ?', $orga);
    $this->orga = $query2->fetchOne();
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
  }

  public function executePlotPresences(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
  }
}

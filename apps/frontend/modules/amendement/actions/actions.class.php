<?php

class amendementActions extends sfActions
{
  static $seuil_amdmts = 8;
  
  public function executeShow(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Amendement')->createquery('a')
      ->where('a.texteloi_id = ?', $request->getParameter('loi'))
      ->andWhere('a.numero = ?', $request->getParameter('numero'))
      ->andWhere('a.sort <> ?', 'Rectifié')
      ->leftJoin('a.ParlementaireAmendement pa')
      ->leftJoin('pa.Parlementaire p');

     $this->amendement = $query->fetchOne();
     $this->forward404Unless($this->amendement);

     if (!($section = $this->amendement->getSection()))
       $this->section = NULL;
     else $this->section = $section->getSection(1);

     $this->identiques = Doctrine::getTable('Amendement')->createQuery('a')
       ->where('content_md5 = ?', $this->amendement->content_md5)
       ->orderBy('numero')
       ->execute();

     if (count($this->identiques) < 2) {
       $this->identiques = array();
     }
     
     $this->seance = $this->amendement->getIntervention($this->amendement->numero);
     foreach($this->identiques as $a) {
       if ($this->seance)
         break;
       $this->seance = $this->amendement->getIntervention($a->numero);
     }

     $this->sous_admts = Doctrine_Query::create()
       ->select('a.id, a.numero')
       ->from('Amendement a, Tagging tg, tg.Tag t')
       ->where('a.texteloi_id = ?', $this->amendement->texteloi_id)
       ->andWhere('a.sort <> ?', 'Rectifié')
       ->andWhere('a.id = tg.taggable_id')
       ->andWhere('t.name LIKE ?', 'loi:sous_amendement_de=%')
       ->andWhere('t.triple_value = ?', $this->amendement->numero)
       ->orderBy('a.numero')
       ->fetchArray();
   
     $this->loi = Doctrine::getTable('TitreLoi')->findLightLoi($this->amendement->texteloi_id);
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')
      ->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->amendements = Doctrine::getTable('Amendement')->createQuery('a')
      ->leftJoin('a.ParlementaireAmendement pa')
      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('a.sort <> ?', 'Rectifié')
    //  ->andWhere('pa.numero_signataire <= ?', self::$seuil_amdmts)
      ->orderBy('a.date DESC, a.texteloi_id DESC, a.numero DESC');
    //    $this->response->setTitle('Les amendements de '.$this->parlementaire->nom);

    $request->setParameter('rss', array(array('link' => '@parlementaire_amendements_rss?slug='.$this->parlementaire->slug, 'title'=>'Les derniers amendements de '.$this->parlementaire->nom.' en RSS')));
  }

  public function executeParlementaireSection(sfWebRequest $request) 
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    $this->section = Doctrine::getTable('Section')->find($request->getParameter('id'));
    $this->forward404Unless($this->section);

    $lois = $this->section->getTags(array('is_triple' => true,
                                          'namespace' => 'loi',
                                          'key' => 'numero',
                                          'return' => 'value'));

    $this->qamendements = Doctrine::getTable('Amendement')->createQuery('a')
      ->leftJoin('a.ParlementaireAmendement pa')
      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('a.sort <> ?', 'Rectifié')
      ->andWhereIn('a.texteloi_id', $lois)
      ->orderBy('a.texteloi_id DESC, a.date DESC, a.numero DESC');
  }

  public function executeSearch(sfWebRequest $request)
  {
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
    $sql = 'SELECT a.id FROM amendement a WHERE MATCH (a.texte,a.expose) AGAINST (\''.implode(' ', $mcle).'\' IN BOOLEAN MODE)';
    $search = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();
    $ids = array();
    foreach($search as $s)
      $ids[] = $s['id'];
    
    $this->query = Doctrine::getTable('Amendement')->createQuery('a');
    if (count($ids))
      $this->query->whereIn('a.id', $ids);
    else if (count($mcle)) foreach($mcle as $m) {
      $this->query->andWhere('a.texte LIKE ?', '% '.$m.' %');
      $this->query->orWhere('a.expose LIKE ?', '% '.$m.' %');
    } else {
      $this->query->where('0');
      return ;
    }

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = Doctrine::getTable('Parlementaire')
        ->findOneBySlug($slug);
      if ($this->parlementaire)
        $this->query->leftJoin('a.ParlementaireAmendement pa')
          ->andWhere('pa.parlementaire_id = ?', $this->parlementaire->id);
    }
    $this->query->orderBy('a.date DESC, a.texteloi_id DESC, a.numero DESC');
    if ($request->getParameter('rss')) {
      $this->setTemplate('rss');
      $this->feed = new sfRssFeed();
    } else $request->setParameter('rss', array(array('link' => '@search_amendements_mots_rss?search='.$this->mots, 'title'=>'Les derniers amendements sur '.$this->mots.' en RSS')));

  }

  public function executeFind(sfWebRequest $request)
  {
    $this->lois = split(',', $request->getParameter('loi'));
    $amdt = $request->getParameter('numero');
    if ($amdt == 'all' || $amdt == 'new' ) {
      if (count($this->lois) == 1)
        $this->loi = Doctrine::getTable('TitreLoi')->findLightLoi($this->lois[0]);
      $this->amendements_query = Doctrine::getTable('Amendement')
        ->createQuery('a')
        ->whereIn('a.texteloi_id', $this->lois)
        ->andWhere('a.sort <> ?', 'Rectifié');
      if ($amdt == 'new')
        $this->amendements_query->orderBy('a.texteloi_id DESC, a.created_at DESC, a.source');
      else $this->amendements_query->orderBy('a.texteloi_id DESC, a.source');
      return ;
    }
    $numeros = array();
    if (count($this->lois) == 1 && preg_match('/^(\d+)([A-Z])$/i', $amdt, $match)) {
      $numero = ($match[1]+0).strtoupper($match[2]);
      $this->redirect('@amendement?loi='.$this->lois[0].'&numero='.$numero);
    } else if (preg_match('/(\d+[A-Z]?)-(\d+[A-Z]?)/i', $amdt, $match)) {
      if (preg_match('/^(\d+)[A-Z]$/i', $match[1], $match2)) $numsta = $match2[1];
      else $numsta = $match[1];
      if (preg_match('/^(\d+)[A-Z]$/i', $match[2], $match2)) $numsto = $match2[1];
      else $numsto = $match[2];
      for($cpt = $numsta ; $cpt <= $numsto ; $cpt++)
        array_push($numeros, $cpt);
    } else {
      preg_match_all('/\D*(\d+)[A-Z]?\D*/i', $amdt, $match);
      $numeros = $match[1];
    }
    $amendements = array();
    foreach($this->lois as $loi) foreach($numeros as $numero) {
      $query = PluginTagTable::getObjectTaggedWithQuery('Amendement', array('loi:amendement='.$numero));
      $query->andWhere('texteloi_id = ?', $loi)
        ->andWhere('sort <> ?', 'Rectifié');
      $res = $query->execute();
      if (count($res)) foreach ($res as $amd) {
        $amendements[$amd->id] = $amd;
      }
    }
    if (count($amendements) == 1) {
      $a = array_keys($amendements);
      $this->redirect('@amendement_id?id='.$a[0]);
    }
    $this->amendements = array_values($amendements);
  }

  public function executeRedirect(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $a = Doctrine::getTable('Amendement')->find($id);
    $this->forward404Unless($a);
    $this->redirect('@amendement?loi='.$a->texteloi_id.'&numero='.$a->numero);
  }
}

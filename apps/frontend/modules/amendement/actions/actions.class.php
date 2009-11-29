<?php

class amendementActions extends sfActions
{
  static $seuil_amdmts = 8;
  
  public function executeShow(sfWebRequest $request)
  {
    $query = doctrine::getTable('Amendement')->createquery('a')
        ->where('a.id = ?', $request->getParameter('id'))
        ->leftJoin('a.ParlementaireAmendement pa')
        ->leftJoin('pa.Parlementaire p');

     $this->amendement = $query->fetchOne();
     $this->forward404Unless($this->amendement);

     if (!($section = $this->amendement->getSection()))
       $this->section = NULL;
     else $this->section = $section->getSection(1);

     $this->identiques = doctrine::getTable('Amendement')->createQuery('a')
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
       ->andWhere('a.id = tg.taggable_id')
       ->andWhere('t.name LIKE ?', 'loi:sous_amendement_de=%')
       ->andWhere('t.triple_value = ?', $this->amendement->numero)
       ->orderBy('a.numero')
       ->fetchArray();
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')
      ->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->amendements = doctrine::getTable('Amendement')->createQuery('a')
      ->leftJoin('a.ParlementaireAmendement pa')
      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
    //  ->andWhere('pa.numero_signataire <= ?', self::$seuil_amdmts)
      ->orderBy('a.date DESC, a.texteloi_id DESC, a.numero DESC');
    //    $this->response->setTitle('Les amendements de '.$this->parlementaire->nom);
  }

  public function executeParlementaireSection(sfWebRequest $request) 
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    $this->section = doctrine::getTable('Section')->find($request->getParameter('id'));
    $this->forward404Unless($this->section);

    $lois = $this->section->getTags(array('is_triple' => true,
                                          'namespace' => 'loi',
                                          'key' => 'numero',
                                          'return' => 'value'));

    $this->qamendements = doctrine::getTable('Amendement')->createQuery('a')
      ->leftJoin('a.ParlementaireAmendement pa')
      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
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
    
    $this->query = doctrine::getTable('Amendement')->createQuery('a');
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
      $this->parlementaire = doctrine::getTable('Parlementaire')
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
    if ($amdt != 'all') {
      $numeros = array();
      if (preg_match('/(\d+)-(\d+)/', $amdt, $match)) {
        for($cpt = 0 ; $cpt < 10 ; $cpt++) {
          if ($match[1]+$cpt > $match[2])
            break;
          array_push($numeros, $match[1]+$cpt);
        }
      } else{
        preg_match_all('/\D*(\d+)\D*/', $amdt, $match);
        $numeros = $match[1];
      }
      $amendements = array();
      foreach($this->lois as $loi) {
        foreach($numeros as $numero) {
          $query = PluginTagTable::getObjectTaggedWithQuery('Amendement', array('loi:amendement='.$numero));
          $query->andWhere('texteloi_id = ?', $loi);
          $res = $query->execute();
          if (count($res)) foreach ($res as $amd) {
            $amendements[$amd->id] = $amd;
          }
        }
      }
      if (count($amendements) == 1) {
        $a = array_keys($amendements);
        $this->redirect('@amendement?id='.$a[0]);
      }
      $this->amendements = array_values($amendements);
    } else {
      $this->amendements_query = doctrine::getTable('Amendement')->createQuery('a')
        ->whereIn('a.texteloi_id', $this->lois)
        ->orderBy('a.texteloi_id DESC, a.numero, a.source');
    }
  }
}

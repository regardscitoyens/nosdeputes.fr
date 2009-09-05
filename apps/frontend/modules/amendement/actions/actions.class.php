<?php

class amendementActions extends sfActions
{
  public function executeShow(sfWebRequest $request)
  {
    $query = doctrine::getTable('Amendement')->createquery('a')
        ->where('a.id = ?', $request->getParameter('id'))
        ->leftJoin('a.ParlementaireAmendement pa')
        ->leftJoin('pa.Parlementaire p');

     $this->amendement = $query->fetchOne();
     $this->forward404Unless($this->amendement);

     $this->section = PluginTagTable::getObjectTaggedWithQuery('Section', array('loi:numero='.$this->amendement->texteloi_id))
       ->fetchOne()->getSection(1);

     $this->identiques = doctrine::getTable('Amendement')->createQuery('a')
       ->where('content_md5 = ?', $this->amendement->content_md5)
       ->orderBy('numero')
       ->execute();

     if (count($this->identiques) < 2) {
       $this->identiques = array();
     }


     $query = PluginTagTable::getObjectTaggedWithQuery('Intervention', array('loi:numero='.$this->amendement->texteloi_id, 'loi:amendement='.$this->amendement->numero));
     $query->select('Intervention.id, Intervention.date, Intervention.seance_id, Intervention.md5')
       ->groupBy('Intervention.date')
       ->orderBy('Intervention.date DESC, Intervention.timestamp ASC');
     $this->seances = $query->fetchArray();
     foreach($this->identiques as $a) {
       if (count($this->seances))
	 break;
       $query = PluginTagTable::getObjectTaggedWithQuery('Intervention', array('loi:numero='.$this->amendement->texteloi_id, 'loi:amendement='.$a->numero));
       $query->select('Intervention.id, Intervention.date, Intervention.seance_id, Intervention.md5')
	 ->groupBy('Intervention.date')
	 ->orderBy('Intervention.date DESC, Intervention.timestamp ASC');
       $this->seances = $query->fetchArray();
     }
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = doctrine::getTable('Parlementaire')
      ->findOneBySlug($request->getParameter('slug'));
    $this->amendements = doctrine::getTable('Amendement')->createQuery('a')
      ->leftJoin('a.ParlementaireAmendement pa')
      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
      ->orderBy('a.date DESC');
  }

  public function executeTop(sfWebRequest $request)
  {
    $q = Doctrine_Query::create()
      ->select('pa.id, p.*, count(pa.amendement_id) as nb')
      ->from('ParlementaireAmendement pa')
      ->leftJoin('pa.Parlementaire p')
      ->groupBy('p.id')
      ->orderBy('nb DESC');
    $this->top = $q->fetchArray();
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
    if (count($mcle) == 0) $sql = 'SELECT a.id FROM amendement a LIMIT 5000';
    else $sql = 'SELECT a.id FROM amendement a WHERE MATCH (a.texte,a.expose) AGAINST (\''.implode(' ', $mcle).'\' IN BOOLEAN MODE)';
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
    $this->query->orderBy('date DESC');
  }

  public function executeFind(sfWebRequest $request)
  {
    $lois = split(',', $request->getParameter('loi'));
    $amdt = $request->getParameter('numero');
    $numeros = array();
    if (preg_match('/(\d+)-(\d+)/', $amdt, $match)) {
      for($cpt = 0 ; $cpt < 10 ; $cpt++) {
	if ($match[1]+$cpt > $match[2])
	  break;
	array_push($numeros, $match[1]+$cpt);
      }
    }else{
      preg_match_all('/\D*(\d+)\D*/', $amdt, $match);
      $numeros = $match[1];
    }
    $amendements = array();
    foreach($lois as $loi) {
      foreach($numeros as $numero) {
	$query = PluginTagTable::getObjectTaggedWithQuery('Amendement', array('loi:amendement='.$numero));
	$query->andWhere('texteloi_id = ?', $loi);
	$res = $query->fetchOne();
	if ($res) {
	  $amendements[$res->id] = $res;
	}
      }
    }
    if (count($amendements) == 1) {
      $a = array_keys($amendements);
      $this->redirect('@amendement?id='.$a[0]);
    }
    $this->amendements = array_values($amendements);
  }


}

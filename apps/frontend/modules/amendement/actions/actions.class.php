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
      ->select('p.*, count(a.id) as nb')
      ->from('Parlementaire p')
      ->leftJoin('p.ParlementaireAmendement pa')
      ->leftJoin('pa.Amendement a')
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

    $sql = 'SELECT a.id FROM amendement a WHERE MATCH (a.texte) AGAINST (\''.implode(' ', $mcle).'\' IN BOOLEAN MODE)';
    $search0 = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();
    $sql = 'SELECT a.id FROM amendement a WHERE MATCH (a.expose) AGAINST (\''.implode(' ', $mcle).'\' IN BOOLEAN MODE)';
    $search1 = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();
    $search = array_merge($search0, $search1);
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
}

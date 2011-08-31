<?php

/**
 * questions actions.
 *
 * @package    cpc
 * @subpackage questions
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class questionsActions extends sfActions
{
  public function executeRedirect(sfWebRequest $request)
  {
    //respect de l'existant : il est possible d'appeler les questions ecrites par leur id
    //Mais lorsque c'est le cas on redirige vers une url plus parlante utilisant le numéro définit par l'AN
    $question = Doctrine::getTable('Question')->find($request->getParameter('id'));
    $this->forward404Unless($question);
    return $this->redirect('@question_numero?numero='.$question->numero.'&legi='.$question->legislature);
  }
  public function executeShow(sfWebRequest $request)
  {
    $numero = strtoupper($request->getParameter('numero'));
    $this->forward404Unless(preg_match('/^(\d*[ACEGS])?(\d+)$/i', $numero, $match));
    if ($match[1]) $numero = $match[1].sprintf("%04d",$match[2]);
    else $numero = sprintf("%05d",$match[2]);
    if (preg_match('/^(\d{2})$/', $request->getParameter('legi'), $match))
      $legi = $match[1];
    else $legi = sfConfig::get('app_legislature', 13);
    $query = Doctrine::getTable('Question')
      ->createquery('q');
    if (preg_match('/^[AECGS]/', $numero))
      $query->where('q.numero LIKE ?', '%'.$numero)
        ->orderBy('q.date DESC');
    else $query->where('q.numero = ?', $numero);
    $this->question = $query->andWhere('q.legislature = ?', $legi)
      ->fetchOne();
    $this->forward404Unless($this->question);
    $this->parlementaire = Doctrine::getTable('Parlementaire')->find($this->question->parlementaire_id);
    $this->forward404Unless($this->parlementaire);
  }

  public function executeParlementaire(sfWebRequest $request) {
    $this->type = $request->getParameter('type');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->questions = Doctrine::getTable('Question')->createQuery('q')
      ->select('q.*, if(q.date_cloture is not null,q.date_cloture,q.date) as date2')
      ->where('q.parlementaire_id = ?', $this->parlementaire->id)
      ->orderBy('date2 DESC, q.numero DESC');
    if ($this->type === "ecrites")
      $this->questions->andWhere('q.type = ?', "Question écrite");
    if ($this->type === "orales")
      $this->questions->andWhere('q.type != ?', "Question écrite");

    $request->setParameter('rss', array(array('link' => '@parlementaire_questions_rss?slug='.$this->parlementaire->slug, 'title'=>'Les dernières questions écrites de '.$this->parlementaire->nom.' en RSS')));
    $this->type = str_replace('ecrites', 'écrites', $this->type);
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

    $sql = 'SELECT i.id FROM question i WHERE type = "Question écrite" AND MATCH (i.question) AGAINST (\''.str_replace("'", "\\'", implode(' ', $mcle)).'\' IN BOOLEAN MODE)';

    $search = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();

    $ids = array();
    foreach($search as $s) {
      $ids[] = $s['id'];
    }

    $this->query = Doctrine::getTable('Question')->createQuery('i')
      ->select('q.*, if(q.date_cloture is not null,q.date_cloture,q.date) as date2');
    if (count($ids))
      $this->query->whereIn('i.id', $ids);
    else if (count($mcle))
      foreach($mcle as $m) {
        $this->query->andWhere('i.type = ?', 'Question écrite')
	  ->andWhere('i.question LIKE ? OR i.reponse LIKE ? OR i.titre LIKE ?', array('% '.$m.' %', '% '.$m.' %', '% '.$m.' %'));
      } else {
      $this->query->where('0');
      return ;
    }

    if ($slug = $request->getParameter('parlementaire')) {
      $this->parlementaire = Doctrine::getTable('Parlementaire')
	->findOneBySlug($slug);
      if ($this->parlementaire)
	$this->query->andWhere('i.parlementaire_id = ?', $this->parlementaire->id);
    } else $this->query->leftJoin('i.Parlementaire p');

    $this->query->orderBy('date2 DESC');
    if ($request->getParameter('rss')) {
      $this->setTemplate('rss');
      $this->feed = new sfRssFeed();
    } else $request->setParameter('rss', array(array('link' => '@search_questions_ecrites_mots_rss?search='.$this->mots, 'title'=>'Les dernières questions écrites sur '.$this->mots.' en RSS')));
  }
}
?>

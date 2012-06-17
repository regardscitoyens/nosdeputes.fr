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
    $question = Doctrine::getTable('QuestionEcrite')->find($request->getParameter('id'));
    $this->forward404Unless($question);
    return $this->redirect('@question_numero?numero='.$question->numero);
  }
  public function executeShow(sfWebRequest $request)
  {
    $numero = $request->getParameter('numero');
    $this->question = Doctrine::getTable('QuestionEcrite')
      ->createquery('q')
      ->where('q.numero = ?', $numero)
      ->andWhere('q.legislature = ?', sfConfig::get('app_legislature', 13))
      ->fetchOne();
    $this->forward404Unless($this->question);
    $this->parlementaire = Doctrine::getTable('Parlementaire')->find($this->question->parlementaire_id);
    $this->forward404Unless($this->parlementaire);
  }

  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    if (myTools::isLegislatureCloturee() && !$this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    $this->questions = Doctrine::getTable('QuestionEcrite')->createQuery('q')
      ->select('q.*, if(q.date_cloture is not null,q.date_cloture,q.date) as date2')
      ->where('q.parlementaire_id = ?', $this->parlementaire->id)
      ->orderBy('date2 DESC, q.numero DESC');

    $request->setParameter('rss', array(array('link' => '@parlementaire_questions_rss?slug='.$this->parlementaire->slug, 'title'=>'Les dernières questions écrites de '.$this->parlementaire->nom.' en RSS')));

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

    $sql = 'SELECT i.id FROM question_ecrite i WHERE MATCH (i.question) AGAINST (\''.str_replace("'", "\\'", implode(' ', $mcle)).'\' IN BOOLEAN MODE)';

    $search = Doctrine_Manager::connection()
      ->getDbh()
      ->query($sql)->fetchAll();

    $ids = array();
    foreach($search as $s) {
      $ids[] = $s['id'];
    }

    $this->query = Doctrine::getTable('QuestionEcrite')->createQuery('i')
      ->select('q.*, if(q.date_cloture is not null,q.date_cloture,q.date) as date2');
    if (count($ids))
      $this->query->whereIn('i.id', $ids);
    else if (count($mcle))
      foreach($mcle as $m) {
	$this->query->andWhere('i.question LIKE ?', '% '.$m.' %');
	$this->query->orWhere('i.reponse LIKE ?', '% '.$m.' %');
	$this->query->orWhere('i.themes LIKE ?', '% '.$m.' %');
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

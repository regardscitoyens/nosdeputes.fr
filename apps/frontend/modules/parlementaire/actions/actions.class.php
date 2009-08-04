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
  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
    $qtag = Doctrine_Query::create();
    $qtag->from('Tagging tg, tg.Tag t, Intervention i');
    $qtag->leftJoin('i.PersonnaliteInterventions pi');
    $qtag->where('pi.parlementaire_id = ?', $this->parlementaire->id);
    $qtag->andWhere('i.id = tg.taggable_id');
    $this->tags = PluginTagTable::getAllTagNameWithCount($qtag, array('model' => 'Intervention', 'triple' => false, 'min_tags_count' => 2));

    asort($this->tags);


    //Ici on cherche à groupes les tags qui sont très similaires
    foreach(array_keys($this->tags) as $tag) {
      $sex = soundex($tag);
      if (isset($sound[$sex])) {
	foreach (array_keys($sound[$sex]) as $word) {
	  $words = preg_split('/\|/', $word);
	  similar_text($tag, $words[0], $pc);
	  if ($pc >= 75) {
	    $ntag = $tag.'|'.$word;
	    $this->tags[$ntag] = $this->tags[$tag] + $this->word[$word];
	    unset($this->tags[$tag]);
	    unset($this->tags[$word]);
	    unset($sound[$sex][$tag]);
	    unset($sound[$sex][$word]);
	    $sound[$sex][$ntag] = 1;
	    continue;
	  }
	}
      }
      $sound[$sex][$tag] = 1;
    }


    //On trie par ordre alpha, et inserre des infos sur l'utilisation des tags (class + count)
    $tot = count($this->tags);
    $cpt = 0;
    asort($this->tags);
    $class = array();
    foreach(array_keys($this->tags) as $tag) {
      $count = $this->tags[$tag];
      unset($this->tags[$tag]);
      $related = preg_split('/\|/', $tag);
      $tag = $related[0];
      $this->tags[$tag] = array();
      $this->tags[$tag]['count'] = $count;
      if (!isset($class[$count]))
	$class[$count] = intval($cpt * 4 / $tot);
      $cpt++;
      $this->tags[$tag]['class'] = $class[$count];
      $this->tags[$tag]['related'] = implode('|', $related);
    }
    ksort($this->tags);



    $this->textes = doctrine_query::create()
      ->from('Section s')
      ->select('s.section_id, sp.titre, count(i.id) as nb')
      ->where('s.section_id = sp.id')
      ->leftJoin('s.Section sp')
      ->leftJoin('s.Interventions i')
      ->leftJoin('i.PersonnaliteIntervention pi')
      ->andWhere('pi.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.nb_mots > 20')
      ->groupBy('s.section_id')
      ->orderBy('nb DESC')
      ->fetchArray();
  }

  public function executeList(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->search = $request->getParameter('search');
    if ($this->search) {
      $query->where('p.nom LIKE ?' , '%'.$this->search.'%');
    }
    $query->orderBy("p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
    if ($this->pager->getNbResults() == 1) {
      $p = $this->pager->getResults();
      return $this->redirect('parlementaire/show?slug='.$p[0]->slug);
    }
  }
  public function executeListCirco(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->circo = $request->getParameter('nom_circo');
    if ($this->circo) {
      $query->where('p.nom_circo = ?', $this->circo);
    }
    $query->orderBy("p.num_circo");
    $query->leftJoin('p.ParlementaireOrganisme po')->leftJoin('po.Organisme o')->andWhere('o.type = "groupe"');
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
  }
  public function executeListProfession(sfWebRequest $request) 
  {
    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    $this->prof = $request->getParameter('profession');
    if ($this->prof) {
      $query->where('p.profession LIKE ?', '%'.$this->prof.'%');
    }
    $query->leftJoin('p.ParlementaireOrganisme po')->leftJoin('po.Organisme o')->andWhere('o.type = "groupe"');
    $query->orderBy("p.nom_de_famille ASC");
    $this->pager = Doctrine::getTable('Parlementaire')->getPager($request, $query);
  }
  public function executeListOrganisme(sfWebRequest $request) 
  {
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
}

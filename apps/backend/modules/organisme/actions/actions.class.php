<?php

require_once dirname(__FILE__).'/../lib/organismeGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/organismeGeneratorHelper.class.php';

/**
 * organisme actions.
 *
 * @package    cpc
 * @subpackage organisme
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class organismeActions extends autoOrganismeActions
{

  public function executeListCommissions(sfWebRequest $request) {
    $query = Doctrine_Query::create()
      ->select('id, nom, slug, po.organisme_id, count(distinct(po.parlementaire_id)) as deputes, s.organisme_id, count(distinct(s.id)) as seances, sum(s.tagged) as tags')
      ->from('Organisme o')
      ->where('o.type = "parlementaire"')
      ->leftJoin('o.ParlementaireOrganismes po')
      ->leftJoin('o.Seances s')
      ->groupBy('o.id')
      ->orderBy('o.nom');
    $this->orgas = $query->fetchArray(); 

    $this->seances = Doctrine_Query::create()
      ->select('id, date, moment, session, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
      ->from('Seance s')
      ->where('s.type = "commission"')
      ->andWhere('(s.organisme_id IS NULL) OR (s.organisme_id = 0)')
      ->leftJoin('s.Interventions i')
      ->leftJoin('s.Presences p')
      ->leftJoin('p.Preuves pr')
      ->groupBy('s.id')
      ->orderBy('s.date, s.moment')
      ->fetchArray();
    
    if ($suppr = $request->getParameter('suppr')) {
      if ($suppr == 'commission')
        $this->delcom = array('id' => $request->getParameter('id'), 'art' => $request->getParameter('art'), 'dep' => $request->getParameter('dep'), 'sea' => $request->getParameter('sea'));
      else if ($suppr == 'seance')
        $this->delsea = array('id' => $request->getParameter('id'), 'pre' => $request->getParameter('pre'), 'prp' => $request->getParameter('prp'));
    } else if ($request->getParameter('result'))
      $this->result = $request->getParameter('result');
  }

  public function executeCommission(sfWebRequest $request) {
    $orga = $request->getParameter('id');
    $this->forward404Unless($orga);
    $this->orga = Doctrine::getTable('Organisme')->find($orga);
    $this->forward404Unless($this->orga);

    $this->deputes = Doctrine_Query::create()
      ->select('nom, slug')
      ->from('Parlementaire p')
      ->leftJoin('p.ParlementaireOrganisme po')
      ->where('po.organisme_id = ?', $orga)
      ->orderBy('po.importance DESC, p.sexe ASC, p.nom_de_famille ASC')
      ->fetchArray();
 
    $this->seances = Doctrine_Query::create()
      ->select('id, date, moment, session, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
      ->from('Seance s')
      ->where('s.organisme_id = ?', $orga)
      ->leftJoin('s.Interventions i')
      ->leftJoin('s.Presences p')
      ->leftJoin('p.Preuves pr')
      ->groupBy('s.id')
      ->orderBy('s.date, s.moment')
      ->fetchArray();

    $this->article = Doctrine::getTable('Article')
      ->createQuery('a')
      ->where('categorie = "Organisme"')
      ->andWhere('object_id = ?', $orga)
      ->fetchOne();

    $this->suppr = 0;
    if ($this->suppr = $request->getParameter('suppr')) {
      if ($request->getParameter('ok')) {
        $query = Doctrine_Query::create()
          ->update('Seance')
          ->set('organisme_id', 'NULL')
          ->where('organisme_id = ?', $orga);
        $sea = $query->execute();
        $query = Doctrine_Query::create()
          ->delete('ParlementaireOrganisme p')
          ->where('p.organisme_id = ?', $orga);
        $dep = $query->execute();
        $query = Doctrine_Query::create()
          ->delete('Article a')
          ->where('a.categorie = "Organisme"')
          ->andWhere('a.object_id = ?', $orga);
        $art = $query->execute();
        $query = Doctrine_Query::create()
          ->delete('Organisme o')
          ->where('o.id = ?', $orga);
        if ($query->execute())
          $this->redirect('@list_commissions_suppr?id='.$orga.'&sea='.$sea.'&dep='.$dep.'&art='.$art);
        else $this->redirect('@list_commissions_suppr?id=0&sea='.$sea.'&dep='.$dep.'&art='.$art);
      } else if ($this->suppr == 2) {
        $this->delsea = array('id' => $request->getParameter('id'), 'pre' => $request->getParameter('pre'), 'prp' => $request->getParameter('prp'));
      }
    } else if ($request->getParameter('result'))
      $this->result = $request->getParameter('result');
  }

  public function executeFuse(sfWebRequest $request) {

    $this->type = $request->getParameter('type');
    $this->forward404Unless($this->type && preg_match('/^(commission|seance)$/', $this->type));
    $this->bad = $request->getParameter('bad');
    $this->good = $request->getParameter('good');
    $this->forward404Unless($this->bad && $this->good && ($this->bad != $this->good));
    if ($this->type == "commission")
      $query = Doctrine::getTable('Organisme')->createQuery('o');
    else $query = Doctrine::getTable('Seance')->createQuery('s');
    $objs = $query->whereIn('id', array($this->bad, $this->good))
      ->execute();
    $this->forward404Unless(count($objs) == 2);

    if ($this->type == "seance") {
      $this->orga = $request->getParameter('id');
      $this->forward404Unless($this->orga);
      $ref_seance = 's.id';
      $result_link = '@commission_fuse_seances?id='.$this->orga.'&result=';
      if ($objs[0]->date != $objs[1]->date)
        $this->redirect($result_link.'wrongdate');
      $query = Doctrine_Query::create()
        ->select('count(id) as ct')
        ->from('Intervention i')
        ->where('i.seance_id = ?', $this->bad);
    } else {
      $ref_seance = 's.organisme_id';
      $result_link = '@list_commissions_fuse?result=';
      $articles = Doctrine::getTable('Article')
        ->createQuery('a')
        ->where('categorie = "Organisme"')
        ->andWhereIn('object_id', array($this->bad, $this->good))
        ->execute();
      $n_art = count($articles);
      if ($n_art == 0)
        $this->article = null;
      else if ($n_art == 1)
        $this->article = $articles[0];
      else $this->redirect($result_link.'wrongart');
      $query = Doctrine_Query::create()
        ->select('count(distinct(parlementaire_id)) as ct')
      ->from('ParlementaireOrganisme p')
      ->where('p.organisme_id = ?', $this->bad);
    }

  // Interdit la fusion d'une commission ayant des parlementaires ou d'une séance ayant des interventions
    $composants = $query->fetchOne();
    if ($composants['ct'] != 0)
      $this->redirect($result_link.'wrong');

    if (!$request->getParameter('ok')) {

      $this->seances = Doctrine_Query::create()
        ->select('id, date, moment, session, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
        ->from('Seance s')
        ->whereIn($ref_seance, array($this->bad, $this->good))
        ->leftJoin('s.Interventions i')
        ->leftJoin('s.Presences p')
        ->leftJoin('p.Preuves pr')
        ->groupBy('s.id')
        ->orderBy('s.date, s.moment')
        ->fetchArray();

      if ($this->type == "commission") $this->deputes = Doctrine_Query::create()
        ->select('nom, slug')
        ->from('Parlementaire p')
        ->leftJoin('p.ParlementaireOrganisme po')
        ->whereIn('po.organisme_id', array($this->bad, $this->good))
        ->orderBy('po.importance DESC, p.sexe ASC, p.nom_de_famille ASC')
        ->fetchArray();

    } else {

      foreach ($objs as $obj)
        if ($obj->id == $this->bad)
          $bad = $obj;
        else $good = $obj;

      if ($this->type == "commission") {

        $corresp = array(strtolower($bad->nom) => strtolower($good->nom));
        $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('commissions');
        if (!$option) {
          $option = new VariableGlobale();
          $option->setChamp('commissions');
          $option->setValue(serialize($corresp));
        } else $option->setValue(serialize(array_merge(unserialize($option->getValue()), $corresp)));
        $option->save();
        
        if ($this->article && $this->article->object_id == $this->bad) {
          $this->article->object_id = $this->good;
          $this->article->save();
        }
        
        $dbh = Doctrine_Manager::getInstance()->connection();
        $dbh->execute("UPDATE seance SET moment = CONCAT(moment,'.',id) WHERE organisme_id = ".$this->bad);
        $query = Doctrine_Query::create()
          ->update('Seance')
          ->set('organisme_id', $this->good)
          ->where('organisme_id = ?', $this->bad)
          ->execute();

        $query = Doctrine_Query::create()
          ->delete('Organisme a');
      } else {

        $this->presences = array();
        foreach (Doctrine_Query::create()->select('id, type, source, pr.parlementaire_id as depute')->from('PreuvePresence p')
          ->leftJoin('p.Presence pr')->where('pr.seance_id = ?', $this->bad)->fetchArray() as $presence) {
          $this->presences[] = $presence['id'];
          $good->addPresenceLight($presence['depute'], $presence['type'], $presence['source']);
        }
        if (preg_match('/^(\d{2}:\d{2})/', $bad->moment, $match))
          $good->setMoment($match[1].'.'.$this->good);
        $good->save();

        if (count($this->presences)) {
          $query = Doctrine_Query::create()
            ->delete('PreuvePresence p')
            ->whereIn('p.id', $this->presences)
            ->execute();
          $query = Doctrine_Query::create()
            ->delete('Presence p')
            ->where('p.seance_id = ?', $this->bad)
            ->execute();
        }

        $query = Doctrine_Query::create()
          ->delete('Seance a');
      }
      if ($query->where('a.id = ?', $this->bad)->execute())
        $result = "good";
      else $result = "fail";
      $this->redirect($result_link.$result);
    }
  }
}

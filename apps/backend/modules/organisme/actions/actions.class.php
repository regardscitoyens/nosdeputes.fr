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
      ->select('id, date, moment, session, organisme_id, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
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
        $this->delsea = array('id' => $request->getParameter('seance'), 'pre' => $request->getParameter('pre'), 'prp' => $request->getParameter('prp'));
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
      ->select('id, date, moment, session, organisme_id, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
      ->from('Seance s')
      ->where('s.organisme_id = ?', $orga)
      ->leftJoin('s.Interventions i')
      ->leftJoin('s.Presences p')
      ->leftJoin('p.Preuves pr')
      ->groupBy('s.id')
      ->orderBy('s.date DESC, s.moment DESC')
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
        $this->delsea = array('id' => $request->getParameter('seance'), 'pre' => $request->getParameter('pre'), 'prp' => $request->getParameter('prp'));
      }
    } else if ($request->getParameter('result'))
      $this->result = $request->getParameter('result');
  }

  public function executeFuse(sfWebRequest $request) {

    $this->type = $request->getParameter('type');
    $this->forward404Unless($this->type && preg_match('/^(commission|seance)$/', $this->type));
    $this->bads = $request->getParameter('bad', $_SESSION["hack_fuse_bad"]);
    $this->goods = $request->getParameter('good', $_SESSION["hack_fuse_good"]);
    $this->forward404Unless($this->bads && $this->goods && ($this->bads != $this->goods));
    unset($_SESSION["hack_fuse_bad"]);
    unset($_SESSION["hack_fuse_good"]);

    if ($this->type == "seance") {
      $this->orga = $request->getParameter('id');
      $this->forward404Unless($this->orga);
      $ref_seance = 's.id';
      if (preg_match('/^(\d+),(\d+)$/', $this->orga, $match))
        $result_link = '@fuse?type=commission&bad='.$match[1].'&good='.$match[2].'&result=';
      else $result_link = '@commission_fuse_seances?id='.$this->orga.'&result=';
      $this->bads_arr = explode(',', $this->bads);
      $this->goods_arr = explode(',', $this->goods);
      $obj_ids = array_merge($this->bads_arr, $this->goods_arr);
      $n_dates = count($this->bads_arr);
      if ($n_dates == 0) $this->redirect($result_link.'wrongdate');
      $objs_arr = array();
      for ($i=0; $i<$n_dates; $i++) {
        $objects = Doctrine::getTable('Seance')
          ->createQuery('s')
          ->whereIn('id', array($this->bads_arr[$i], $this->goods_arr[$i]))
          ->execute();
        $this->forward404Unless(count($objects) == 2);
        if ($objects[0]->date != $objects[1]->date)
          $this->redirect($result_link.'wrongdate');
        $objs_arr[$i] = $objects;
      }
      if ($request->getParameter('doublons'))
        $this->doublons = 1;
      $query = Doctrine_Query::create()
        ->select('count(id) as ct')
        ->from('Intervention i')
        ->whereIn('i.seance_id', $this->bads_arr);
    } else {
      $ref_seance = 's.organisme_id';
      $result_link = '@list_commissions_fuse?result=';
      $this->bad = $this->bads;
      $this->good = $this->goods;
      $obj_ids = array($this->bad, $this->good);
      $objs = Doctrine::getTable('Organisme')
        ->createQuery('o')
        ->whereIn('id', $obj_ids)
        ->execute();
      $this->forward404Unless(count($objs) == 2);
      $articles = Doctrine::getTable('Article')
        ->createQuery('a')
        ->where('categorie = "Organisme"')
        ->andWhereIn('object_id', $obj_ids)
        ->execute();
      $n_art = count($articles);
      if ($n_art == 0)
        $this->article = null;
      else if ($n_art == 1)
        $this->article = $articles[0];
      else $this->redirect($result_link.'wrongart');
      $doublons_q = Doctrine_Query::create()
        ->select('id, date, moment, i.seance_id, count(distinct(i.id)) as n_interventions')
        ->from('Seance s')
        ->whereIn($ref_seance, $obj_ids)
        ->leftJoin('s.Interventions i')
        ->groupBy('s.id')
        ->orderBy('s.date, s.moment')
        ->fetchArray();
      if ($doublons_q) {
        $tmpstr = "";
        $doublons_bad = "";
        $doublons_good = "";
        foreach($doublons_q as $doublon) {
          if ($tmpstr != $doublon['date'].$doublon['moment']) {
            $tmpstr = $doublon['date'].$doublon['moment'];
            $tmpid = $doublon['id'];
          } else {
            if ($doublon['n_interventions'] == 0) {
              $bd = $doublon['id'];
              $gd = $tmpid;
            } else {
              $gd = $doublon['id'];
              $bd = $tmpid;
            }
            if ($doublons_bad == "") {
              $doublons_bad .= $bd;
              $doublons_good .= $gd;
            } else {
              $doublons_bad .= ','.$bd;
              $doublons_good .= ','.$gd;
            }
          }  
        }
        if ($doublons_bad != "" && $doublons_good != "" && ($doublons_bad != $doublons_good)) {
          $_SESSION["hack_fuse_bad"] = $doublons_bad;
          $_SESSION["hack_fuse_good"] = $doublons_good;
          $this->redirect('@fuse?type=seance&id='.$this->bad.','.$this->good.'&doublons=1');
        }
      }
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
      if ($request->getParameter('result'))
        $this->result = $request->getParameter('result');

      $this->seances = Doctrine_Query::create()
        ->select('id, date, moment, session, organisme_id, tagged, nb_commentaires, i.seance_id, count(distinct(i.id)) as n_interventions, p.seance_id, count(distinct(p.parlementaire_id)) as presents, count(distinct(pr.type)) as sources')
        ->from('Seance s')
        ->whereIn($ref_seance, $obj_ids)
        ->leftJoin('s.Interventions i')
        ->leftJoin('s.Presences p')
        ->leftJoin('p.Preuves pr')
        ->groupBy('s.id')
        ->orderBy('s.date, s.moment')
        ->fetchArray();

      if ($this->type == "commission")
         $this->deputes = Doctrine_Query::create()
          ->select('nom, slug')
          ->from('Parlementaire p')
          ->leftJoin('p.ParlementaireOrganisme po')
          ->whereIn('po.organisme_id', $obj_ids)
          ->orderBy('po.importance DESC, p.sexe ASC, p.nom_de_famille ASC')
          ->fetchArray();

    } else {
      if ($this->type == "commission") {
        foreach ($objs as $obj)
          if ($obj->id == $this->bad)
            $bad = $obj;
          else $good = $obj;
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
        
        $query = Doctrine_Query::create()
          ->update('Seance')
          ->set('organisme_id', $this->good)
          ->where('organisme_id = ?', $this->bad)
          ->execute();

        $query = Doctrine_Query::create()
          ->delete('Organisme o')
          ->where('o.id = ?', $this->bad);
        if ($query->execute())
          $results = 'good';
        else $results = 'fail';
      } else for ($i=0; $i<$n_dates; $i++) {
        $this->bad = $this->bads_arr[$i];
        $this->good = $this->goods_arr[$i];
        foreach ($objs_arr[$i] as $obj)
          if ($obj->id == $this->bad)
            $bad = $obj;
          else $good = $obj;
        $this->presences = array();
        foreach (Doctrine_Query::create()->select('id, type, source, pr.parlementaire_id as depute')->from('PreuvePresence p')
          ->leftJoin('p.Presence pr')->where('pr.seance_id = ?', $this->bad)->fetchArray() as $presence) {
          $this->presences[] = $presence['id'];
          $good->addPresenceLight($presence['depute'], $presence['type'], $presence['source']);
        }
        if (preg_match('/^(\d{2}:\d{2})/', $bad->moment, $match)) {
          $goodmom = $match[1];
          if (!($good->moment == $goodmom)) {
            if ($bad->moment == $goodmom && $good->organisme_id == $bad->organisme_id) {
              $bad->moment = $goodmom.'temp';
              $bad->save();
            }
            $good->setMoment($goodmom);
          }
        }
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
          ->delete('Seance s')
          ->where('s.id = ?', $this->bad);
        if (! $query->execute()) {
          $results = 'fail';
          break;
        } else $results = 'good';
      }
      $this->redirect($result_link.$results);
    }
  }
}

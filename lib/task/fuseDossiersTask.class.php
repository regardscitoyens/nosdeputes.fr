<?php

class fuseDossiersTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'fuse';
    $this->name = 'Dossiers';
    $this->briefDescription = 'Fusionne un dossier vers un autre';
    $this->addArgument('baddossier', sfCommandArgument::REQUIRED, 'Dossier à intégrer'); 
    $this->addArgument('gooddossier', sfCommandArgument::REQUIRED, 'Dossier d\'acccueil');
    $this->addArgument('seanceid', sfCommandArgument::OPTIONAL, 'Optionnel : limite à cette séance');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $bad = Doctrine::getTable('Section')->find($arguments['baddossier']);
    $good = Doctrine::getTable('Section')->find($arguments['gooddossier']);
    $seance = $arguments['seanceid'];
    if (!$bad || !$good) {
      print "Dossier inexistant\n";
      return;
    }
    if ($good->id != $good->section_id) {
      print "Le dossier d'accueil est une sous section\n";
      return;
    }
    if ($bad->id == $good->id)  {
      print "Même dossier\n";
      return;
    }

    #print " - Gère les sous-sections\n";
    $n_itv = 0;
    foreach ($bad->SubSections as $sub) {
      if ($sub->id == $bad->id) continue;
      print "\n + $sub->titre_complet";
      $exist = Doctrine::getTable('Section')->createQuery('s')->where('s.section_id = ?', $good->id)->andWhere('s.titre = ?', $sub->getOrigTitre())->fetchOne();
      if (isset($exist->section_id)) {
        print " existe déjà pour la section d'accueil, met-à-jour\n";

        $this->updateTags($sub, $exist);
        $n_itv += $this->updateInterv($sub, $exist);
        $this->updateComments($sub, $exist);
        $this->updateMinDate($sub, $exist);

        $query = Doctrine_Query::create()
          ->delete('Section s')
          ->where('s.id = ?', $sub->id);
        if (! $query->execute()) {
          print "\n  -> Suppression impossible de la sous-section $sub->id\n";
          return;
        } else print " fusionnée et supprimée\n";

      } else {
        $sub->setTitreComplet(str_replace($bad->getOrigTitre(), $good->getOrigTitre(), $sub->titre_complet));
        $sub->section_id = $good->id;
        print " -> $sub->titre_complet";
        $sub->save();
        $n_itv += $sub->nb_interventions;
        $sub->nb_commentaires;
      }
    }
 
  if ($bad->id != $bad->section_id) {
    print "\n + $sub->titre_complet";
    $exist = Doctrine::getTable('Section')->createQuery('s')->where('s.section_id = ?', $good->id)->andWhere('s.titre = ?', $bad->getOrigTitre())->fetchOne();
    if (isset($exist->section_id)) {
      print " existe déjà pour la section d'accueil, met-à-jour\n";
      if ($seance) $n_itv += $this->updateInterv($bad, $exist, 0, $seance);
      else {
        $this->updateTags($bad, $exist);
        $n_itv += $this->updateInterv($bad, $exist);
        $this->updateComments($bad, $exist);
        $this->updateMinDate($bad, $exist);
        $query = Doctrine_Query::create()
          ->delete('Section s')
          ->where('s.id = ?', $bad->id);
        if (! $query->execute()) {
          print "\n  -> Suppression impossible de la sous-section $bad->id\n";
          return;
        } else print " fusionnée et supprimée\n";
      }
    } else {
      if ($seance) {
        $new = new Section();
        $new->setTitreComplet(str_replace($bad->Section->getOrigTitre(), $good->getOrigTitre(), $bad->titre_complet));
        $new->section_id = $good->id;
        print " -> $new->titre_complet";
        $this->updateInterv($bad, $new, 0, $seance);
        $new->save();
      } else {
        $bad->setTitreComplet(str_replace($bad->getOrigTitre(), $good->getOrigTitre(), $bad->titre_complet));
        $bad->section_id = $good->id;
        print " -> $bad->titre_complet";
        $bad->save();
        $n_itv += $bad->nb_interventions;
        $bad->nb_commentaires;
      }
    }

  } else { 
    $this->updateTags($bad, $good);
    $this->updateInterv($bad, $good, $n_itv);
    $this->updateComments($bad, $good);
    $this->updateMinDate($bad, $good);
 
    $corresp = array(strtolower($bad->getOrigTitre()) => strtolower($good->getOrigTitre()));
    print "\nEnregistre la correspondance en base :\n";
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('dossiers');
    if (!$option) {
      $option = new VariableGlobale();
      $option->setChamp('dossiers');
      $option->setValue(serialize($corresp));
    } else $option->setValue(serialize(array_merge(unserialize($option->getValue()), $corresp)));
    $option->save();
    print_r($corresp);
    print "  ";
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('linkdossiers');
    if (!$option) {
      $option = new VariableGlobale();
      $option->setChamp('linkdossiers');
      $option->setValue(serialize(array("$bad->id" => "$good->id")));
    } else {
      $value = unserialize($option->getValue());
      $value["$bad->id"] = "$good->id";
      $option->setValue(serialize($value));
    }
    $option->save();
    print "$bad->id => $good->id\n";

    $query = Doctrine_Query::create()
      ->delete('Section s')
      ->where('s.id = ?', $bad->id);
    if (! $query->execute()) {
      print 'Suppression impossible de la section '.$bad->id."\n";
    }
  }
 }

  private static function updateTags($b, $g) {
    #print "      Gère les tags\n";
    foreach(Doctrine::getTable('Tagging')->createQuery('t')->where('t.taggable_model = ?', 'Section')->andWhere('t.taggable_id = ?', $b->id)->execute() as $tag) {
      #print $tag->tag_id." ";
      if (count(Doctrine::getTable('Tagging')->createQuery('t')->where('t.taggable_model = ?', 'Section')->andWhere('t.taggable_id = ?', $g->id)->andWhere('t.tag_id = ?', $tag->tag_id)->execute()) > 0) {
         $query = Doctrine_Query::create()
           ->delete('Tagging t')
           ->where('t.id = ?', $tag->id);
         if (! $query->execute()) {
           print 'Impossible de supprimer le tagging '.$tag->id."\n";
         }
      } else {
        $tag->taggable_id = $g->id;
        $tag->save();
      }
    }
  }

  private static function updateInterv($b, $g, $base = 0, $seance = 0) {
    #print "      Gère les interventions\n";
    $ct = 0;
    $query = Doctrine::getTable('Intervention')->createQuery('i')->where('i.section_id = ?', $b->id);
    if ($seance) $query->andWhere('i.seance_id = ?', $seance);
    foreach($query->execute() as $itv) {
      #print $itv->id." ";
      $itv->section_id = $g->id;
      $itv->save();
      $ct++;
    }
    if ($ct != 0) {
      $query = Doctrine_Query::create()
        ->update('Section')
        ->set('nb_interventions', $g->nb_interventions + $ct + $base)
        ->where('id = ?', $g->id)
        ->execute();
    }
    #print " / total : ".$ct."\n";
    return $ct;
  }

  private static function updateComments($b, $g) {
    #print "      Gère les commentaires\n";
    $ct = 0;
    foreach(Doctrine::getTable('CommentaireObject')->createQuery('c')->where('c.object_type = ?', 'Section')->andWhere('c.object_id = ?', $b->id)->execute() as $com) {
      #print $com->id." ";
      $com->object_id = $g->id;
      $com->save();
      $comment = $com->getCommentaire();
      if ($comment->is_public == 1) $ct++;
    }
    if ($ct != 0) {
      $query = Doctrine_Query::create()
        ->update('Section')
        ->set('nb_commentaires', $g->nb_commentaires + $ct)
        ->where('id = ?', $g->id)
        ->execute();
    }
    #print "\n";
  }

  private static function updateMinDate($b, $g) {
    if (strtotime($b->min_date) < strtotime($g->min_date))
      $query = Doctrine_Query::create()
        ->update('Section')
        ->set('min_date', '"'.$b->min_date.'"')
        ->where('id = ?', $g->id)
        ->execute();
  }

}


<?php

class fuseDossiersTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'fuse';
    $this->name = 'Dossiers';
    $this->briefDescription = 'Fusionne un dossier vers un autre';
    $this->addArgument('baddossier', sfCommandArgument::REQUIRED, 'Dossier à intégrer'); 
    $this->addArgument('gooddossier', sfCommandArgument::REQUIRED, 'Dossier d\'acccueil');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $bad = Doctrine::getTable('Section')->find($arguments['baddossier']);
    $good = Doctrine::getTable('Section')->find($arguments['gooddossier']);
    if (!$bad || !$good) {
      print "Dossier inexistant\n";
      return;
    }
    if ($bad->id != $bad->section_id || $good->id != $good->section_id) {
      print "Un dossier est une sous section\n";
      return;
    }
    if ($bad->id == $good->id)  {
      print "Même dossier\n";
      return;
    }

    print " - Gère les sous-sections\n";
    $n_itv = 0;
    $n_com = 0;
    foreach ($bad->SubSections as $sub) {
      if ($sub->id == $bad->id) continue;
      print "   + ".$sub->titre_complet."\n";
      $exist = Doctrine::getTable('Section')->createQuery('s')->where('s.section_id = ?', $good->id)->andWhere('s.titre = ?', $sub->titre)->fetchOne();
      if (isset($exist->section_id)) {
        print "      existe déjà pour la section d'accueil, met-à-jour\n";

        $this->updateTags($sub, $exist);
        $n_itv += $this->updateInterv($sub, $exist);
        $n_com += $this->updateComments($sub, $exist);
        $this->updateMinDate($sub, $exist);

        $query = Doctrine_Query::create()
          ->delete('Section s')
          ->where('s.id = ?', $sub->id);
        if (! $query->execute()) {
          print 'Suppression impossible de la sous-section '.$sub->id."\n";
          return;
        } else print "      Ancienne sous-section fusionnée et supprimée\n";

      } else {
        print "      change le titre et le numéro de section mère\n";
        $sub->setTitreComplet(str_replace($sub->titre, $good->titre, $sub->titre_complet));
        $sub->section_id = $good->id;
        $sub->save();
        $n_itv += $sub->nb_interventions;
        $n_com += $sub->nb_commentaires;
      }
    }
  
    $this->updateTags($bad, $good);
    $this->updateInterv($bad, $good, $n_itv);
    $this->updateComments($bad, $good, $n_com);
    $this->updateMinDate($bad, $good);
 
    $corresp = array(strtolower($bad->titre) => strtolower($good->titre));
    print "Enregistre la correspondance en base :\n";
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('dossiers');
    if (!$option) {
      $option = new VariableGlobale();
      $option->setChamp('dossiers');
      $option->setValue(serialize($corresp));
    } else $option->setValue(serialize(array_merge(unserialize($option->getValue()), $corresp)));
    $option->save();
    print_r(unserialize($option->getValue()));
    print "\n";

    $query = Doctrine_Query::create()
      ->delete('Section s')
      ->where('s.id = ?', $bad->id);
    if (! $query->execute()) {
      print 'Suppression impossible de la section '.$bad->id."\n";
    } else print "Done\n";
  }

  private static function updateTags($b, $g) {
    print "      Gère les tags\n";
    foreach(Doctrine::getTable('Tagging')->createQuery('t')->where('t.taggable_model = ?', 'Section')->andWhere('t.taggable_id = ?', $b->id)->execute() as $tag) {
      print $tag->tag_id." ";
      if (Doctrine::getTable('Tagging')->createQuery('t')->where('t.taggable_model = ?', 'Section')->andWhere('t.taggable_id = ?', $g->id)->andWhere('t.tag_id = ?', $tag->tag_id)) {
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
    print "\n";
  }

  private static function updateInterv($b, $g, $base = 0) {
    print "      Gère les interventions\n";
    $ct = 0;
    foreach(Doctrine::getTable('Intervention')->createQuery('i')->where('i.section_id = ?', $b->id)->execute() as $itv) {
      print $itv->id." ";
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
    print "\n";
    return $ct;
  }

  private static function updateComments($b, $g, $base = 0) {
    print "      Gère les commentaires\n";
    $ct = 0;
    foreach(Doctrine::getTable('CommentaireObject')->createQuery('c')->where('c.object_type = ?', 'Section')->andWhere('c.object_id = ?', $b->id)->execute() as $com) {
      print $com->id." ";
      $com->object_id = $g->id;
      $com->save();
      $ct++;
    }
    if ($ct != 0) {
      $query = Doctrine_Query::create()
        ->update('Section')
        ->set('nb_commentaires', $g->nb_commentaires + $ct + $base)
        ->where('id = ?', $g->id)
        ->execute();
    }
    print "\n";
    return $ct;
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


<?php

class moveSeanceTask extends sfBaseTask {

  protected function configure() {
    $this->namespace = 'move';
    $this->name = 'Seance';
    $this->briefDescription = 'Déplace les interventions d\'une séance d\'un dossier vers un autre';
    $this->addArgument('seance', sfCommandArgument::REQUIRED, 'Séance à déplacer');
    $this->addArgument('baddossier', sfCommandArgument::REQUIRED, 'Dossier d\'origine'); 
    $this->addArgument('gooddossier', sfCommandArgument::REQUIRED, 'Dossier d\'acccueil');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {

    $manager = new sfDatabaseManager($this->configuration);
    $seance = Doctrine::getTable('Seance')->find($arguments['seance']);
    $bad = Doctrine::getTable('Section')->find($arguments['baddossier']);
    $good = Doctrine::getTable('Section')->find($arguments['gooddossier']);
    if (!$bad || !$good || !$seance) {
      print "Dossier ou séance inexistant\n";
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

    $ni = 0;
    $nc = 0;
    $badcurrent = $bad;
    $goodsub = Doctrine::getTable('Section')->createQuery('s')->where('s.section_id = ?', $good->id)->andWhere('s.titre = ?', $bad->getOrigTitre())->fetchOne();
    $nigoodsub = 0;
    $ncgoodsub = 0;
    foreach(Doctrine::getTable('Intervention')->createQuery('i')->leftJoin('i.Section s')->where('i.seance_id = ?', $seance->id)->andWhere('s.section_id = ?', $bad->id)->orderBy('i.timestamp')->execute() as $itv) {
#print "Intervention N°".$itv->id."\n";
      if (preg_match('/ > discussion /i', $itv->Section->titre_complet) && preg_match('/^question/i', $good->titre_complet)) break;
      if ($itv->section_id != $bad->id) {
        if ($badcurrent->id != $itv->section_id) {
          if ($badcurrent->id != $bad->id)
            $this->fixDossiers($goodcurrent, $badcurrent, $seance, $nisub, $ncsub);
          $nisub = 0;
          $ncsub = 0;
          $badcurrent = $itv->Section;
          print " + ".$badcurrent->titre_complet;
          $goodcurrent = Doctrine::getTable('Section')->createQuery('s')->where('s.section_id = ?', $good->id)->andWhere('s.titre = ?', $badcurrent->getOrigTitre())->fetchOne();
          if (!isset($goodcurrent->section_id)) {
            $goodcurrent = new Section();
            $goodcurrent->setTitreComplet(str_replace(strtolower($bad->getOrigTitre()), strtolower($good->getOrigTitre()), $badcurrent->titre_complet));
            $goodcurrent->min_date = $seance->date;
            $goodcurrent->timestamp = $badcurrent->timestamp;
            $goodcurrent->section_id = $good->id;
            $goodcurrent->save();
          }
          print " -> ".$goodcurrent->titre_complet."\n";
        }
        $itv->section_id = $goodcurrent->id;
        $coms = $this->updateComments($goodcurrent, $badcurrent, $itv);
        $nisub++;
        $ncsub += $coms;
      } else {
        if (isset($goodsub->section_id)) {
          $itv->section_id = $goodsub->id;
          $coms = $this->updateComments($goodsub, $bad, $itv);
          $nigoodsub++;
          $ncgoodsub += $coms;
        } else {
          $itv->section_id = $good->id;
          $coms = $this->updateComments($good, $bad, $itv);
        }
      }
      $nc += $coms;
      $ni++;
      if (preg_match('/^question/i', $good->titre_complet))
        $itv->type = "question";
      $itv->save();
    }
    if (isset($goodsub->section_id) && $nigoodsub > 0) {
      $this->updateCounts($goodsub, $nigoodsub, $ncgoodsub);
      $this->setMinDate($seance, $goodsub);
    }
    if ($itv->section_id != $good->id)
      $this->fixDossiers($goodcurrent, $badcurrent, $seance, $nisub, $ncsub);
    $this->updateCounts($bad, -$ni, -$nc);
    $this->updateCounts($good, $ni, $nc);
    $this->setMinDate($seance, $good);
  }

  private static function setMinDate($s, $g) {
    if (strtotime($s->date) < strtotime($g->min_date))
      $query = Doctrine_Query::create()
        ->update('Section')
        ->set('min_date', '"'.$s->date.'"')
        ->where('id = ?', $g->id)
        ->execute();
  }

  private static function fixDossiers($good, $bad, $seance, $ni, $nc) {
    self::updateCounts($good, $ni, $nc);
    self::setMinDate($seance, $good);
    if ($bad->nb_interventions - $ni < 1) {
      $query = Doctrine_Query::create()
        ->delete('Section s')
        ->where('id = ?', $bad->id);
      if (!$query->execute())
        print "Impossible de supprimer la section vide N°$bad->id : $bad->titre_complet";
      #else print "Section vide N°$bad->id : $bad->titre_complet supprimée";
    } else self::updateCounts($bad, -$ni, -$nc);
  }

  private static function updateCounts($s, $ni, $nc) {
    if ($ni != 0) Doctrine_Query::create()
      ->update('Section')
      ->set('nb_interventions', $s->nb_interventions + $ni)
      ->where('id = ?', $s->id)
      ->execute();
    if ($nc != 0) Doctrine_Query::create()
      ->update('Section')
      ->set('nb_commentaires', $s->nb_commentaires + $nc)
      ->where('id = ?', $s->id)
      ->execute();
  }

  private static function updateComments($g, $b, $i) {
    $ct = 0;
    foreach(Doctrine::getTable('Commentaire')->createQuery('c')->where('c.object_type = ?', 'Intervention')->andWhere('c.object_id = ?', $i->id)->execute() as $com) {
      #print "com ".$com->id."; ";
      $query = Doctrine_Query::create()
        ->delete('CommentaireObject c')
        ->where('c.object_type = ?', 'Section')
        ->andWhere('c.object_id = ?', $b->id)
        ->andWhere('c.commentaire_id = ?', $com->id);
      if (! $query->execute()) {
        print 'Impossible de supprimer le comment '.$com->id."\n";
      }
      $newcom = new CommentaireObject();
      $newcom->object_id = $g->id;
      $newcom->commentaire_id = $com->id;
      $newcom->object_type = 'Section';
      $newcom->save();
      $comment = $newcom->getCommentaire();
      if ($comment->is_public == 1) $ct++;
    }
    return $ct;
  }
}


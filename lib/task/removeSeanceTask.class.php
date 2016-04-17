<?php

class removeSeanceTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'Seance';
    $this->briefDescription = 'Supprimer proprement une séance et ses dépendances';
    $this->addArgument('id', sfCommandArgument::REQUIRED, 'Id de la séance à supprimer');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->addOption('withcomments', null, sfCommandOption::PARAMETER_OPTIONAL, 'force seance with comments', false);
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $seance = Doctrine::getTable('Seance')->find($arguments['id']);
    if (!$seance) {
      print "Séance inexistante\n";
      return;
    }
    $id = $seance->id;
    print $id;
    if (Doctrine_Query::create()->select('c.id')->from('Commentaire c, Intervention i')->where('i.id = c.object_id')->andWhere('c.object_type = "Intervention"')->andWhere('i.seance_id = ?', $id)->fetchOne()) {

      print ": Un ou plusieurs commentaires sont associés à cette séance, veillez à corriger cela en premier lieu\n";
      if (!$options['withcomments']) {
          return;
      }
    }

    print " - Gère les présences\n";
    foreach (Doctrine_Query::create()->select('id')->from('Presence')->where('seance_id = ?', $id)->fetchArray() as $presence) {
      $pres = $presence['id'];
      print $pres;
      $query = Doctrine_Query::create()
        ->delete('PreuvePresence p')
        ->where('p.presence_id = ?', $pres)
        ->andWhereIn('p.type', array("intervention", "compte-rendu"))
        ->execute();
      if (Doctrine_Query::create()->select('id')->from('PreuvePresence')->where('presence_id = ?', $pres)->fetchOne()) {
        print "(kept via JO)";
      } else {
        $query = Doctrine_Query::create()
          ->delete('Presence p')
          ->where('p.id = ?', $pres);
        if (! $query->execute()) {
          print 'Suppression impossible de la présence N°'.$pres."\n";
          return;
        }
      }
      print "//";
    }

    $sections = array();
    print "\n - Gère les interventions et leurs tags\n";
    foreach (Doctrine_Query::create()->select('id, section_id')->from('Intervention')->where('seance_id = ?', $id)->fetchArray() as $intervention) {
      $inter = $intervention['id'];
      if (!isset($sections[$intervention['section_id']]))
        $sections[$intervention['section_id']] = 1;
      print $inter."//";
      $query = Doctrine_Query::create()
        ->delete('Tagging t')
        ->where('t.taggable_model = ?', 'Intervention')
        ->andWhere('t.taggable_id = ?', $inter)
        ->execute();
      $query = Doctrine_Query::create()
        ->delete('Intervention i')
        ->where('i.id = ?', $inter);
      if (! $query->execute()) {
        print 'Suppression impossible de l\'intervention N°'.$inter."\n";
        return;
      }
    }

    $parents = array();
    print "\n - Gère les éventuelles sections vides\n";
    if (count($sections)) {
      foreach (Doctrine_Query::create()->select('id, section_id')->from('Section')->whereIn('id', array_keys($sections))->fetchArray() as $section) {
        $sec = $section['id'];
        if ($sec == $section['section_id']) {
          if (!isset($parents[$sec]))
            $parents[$sec] = 1;
        } else {
          $nb_inter = Doctrine_Query::create()->select('count(id) as ct')->from('Intervention')->where('section_id = ?', $sec)->fetchOne();
          if (!$nb_inter['ct']) {
            print $sec."//";
            $query = Doctrine_Query::create()
              ->delete('Section s')
              ->where('s.id = ?', $sec);
            if (! $query->execute()) {
              print 'Suppression impossible de la section N°'.$sec."\n";
              return;
            }
          }
        }
      }
      foreach (array_keys($parents) as $sid) {
        $nb_inter = Doctrine_Query::create()->select('count(distinct(i.id)) as ct')->from('Intervention i, Section s')->where('i.section_id = ? AND s.id = ?', array($sid, $sid))->orWhere('i.section_id = s.id and s.section_id = ?', $sid)->fetchOne();
        if (!$nb_inter['ct']) {
          print $sid."//";
          $query = Doctrine_Query::create()
            ->delete('Section s')
            ->where('s.id = ?', $sid);
          if (! $query->execute()) {
            print 'Suppression impossible de la section N°'.$sid."\n";
            return;
          }
        }
      }
    }

    print "\n";
    $query = Doctrine_Query::create()
      ->delete('Seance s')
      ->where('s.id = ?', $id);
    if (! $query->execute()) {
      print 'Suppression impossible de la séance '.$id."\n";
      return;
    }
    print "Séance ".$id." supprimée avec ses dépendances\n";
  }
}


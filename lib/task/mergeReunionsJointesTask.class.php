<?php

class mergeReunionsJointesTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'merge';
    $this->name = 'ReunionsJointes';
    $this->briefDescription = "Fusionner proprement une réunion conjointe dans une autre (déplace les présences de la première dans la seconde et en supprime les interventions désirées pour une didascalie pointant un lien vers l'autre";
    $this->addArgument('badid', sfCommandArgument::REQUIRED, 'Id de la séance à expurger des doublons');
    $this->addArgument('gdid', sfCommandArgument::REQUIRED, "Id de la séance d'accueil");
    $this->addArgument('firstinter', sfCommandArgument::REQUIRED, "Id de la première intervention à supprimer");
    $this->addArgument('lastinter', sfCommandArgument::REQUIRED, "Id de la dernière intervention à supprimer");
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $badseance = Doctrine::getTable('Seance')->find($arguments['badid']);
    if (!$badseance) {
      print "Séance ".$arguments['badid']." inexistante\n";
      return;
    }
    $gdseance = Doctrine::getTable('Seance')->find($arguments['gdid']);
    if (!$gdseance) {
      print "Séance ".$arguments['gdid']." inexistante\n";
      return;
    }
    $firstinter = Doctrine::getTable('Intervention')->find($arguments['firstinter']);
    if (!$firstinter) {
      print "Intervention ".$arguments['firstinter']." inexistante\n";
      return;
    }
    if ($firstinter->seance_id != $badseance->id) {
      print "L'intervention ".$arguments['firstinter']." n'appartient pas à la séance ".$badseance->id."\n";
      return;
    }
    $lastinter = Doctrine::getTable('Intervention')->find($arguments['lastinter']);
    if (!$lastinter) {
      print "Intervention ".$arguments['lastinter']." inexistante\n";
      return;
    }
    if ($lastinter->seance_id != $badseance->id) {
      print "L'intervention ".$arguments['lastinter']." n'appartient pas à la séance ".$badseance->id."\n";
      return;
    }

    # Merge présences bad seance into good seance
    # check if presence existante in good seance
    # - if yes, add to it all preuves from bad seance and remove bad presence
    # - if not, just change seance_id into bad presence for gdseance->id

    # Prepare didascalie ref
    # didasc->intervention = "<p>Le compte-rendu de cette réunion conjointe est lisible à l'adresse suivante .</p>"
    # didasc->source = $firstinter->source;
    # didasc->timestamp = $firstinter->timestamp;

    # Remove interventions from bad seance
    # - get all interv in timestamp order, when found firstinter->id, start removing (or replace first with didasc), end when lastinter->id found (included)
    # - gérer les tags associés

    # Add didascalie remplaçante into bad seance



#    if (Doctrine_Query::create()->select('c.id')->from('Commentaire c, Intervention i')->where('i.id = c.object_id')->andWhere('c.object_type = "Intervention"')->andWhere('i.seance_id = ?', $id)->fetchOne()) {
#
#      print ": Un ou plusieurs commentaires sont associés à cette séance, veillez à corriger cela en premier lieu\n";
#      if (!$options['withcomments']) {
#          return;
#      }
#    }
#
#    print " - Gère les présences\n";
#    foreach (Doctrine_Query::create()->select('id')->from('Presence')->where('seance_id = ?', $id)->fetchArray() as $presence) {
#      $pres = $presence['id'];
#      print $pres;
#      $query = Doctrine_Query::create()
#        ->delete('PreuvePresence p')
#        ->where('p.presence_id = ?', $pres)
#        ->andWhereIn('p.type', array("intervention", "compte-rendu"))
#        ->execute();
#      if (Doctrine_Query::create()->select('id')->from('PreuvePresence')->where('presence_id = ?', $pres)->fetchOne()) {
#        print "(kept via JO)";
#      } else {
#        $query = Doctrine_Query::create()
#          ->delete('Presence p')
#          ->where('p.id = ?', $pres);
#        if (! $query->execute()) {
#          print 'Suppression impossible de la présence N°'.$pres."\n";
#          return;
#        }
#      }
#      print "//";
#    }
#
#    $sections = array();
#    print "\n - Gère les interventions et leurs tags\n";
#    foreach (Doctrine_Query::create()->select('id, section_id')->from('Intervention')->where('seance_id = ?', $id)->fetchArray() as $intervention) {
#      $inter = $intervention['id'];
#      if (!isset($sections[$intervention['section_id']]))
#        $sections[$intervention['section_id']] = 1;
#      print $inter."//";
#      $query = Doctrine_Query::create()
#        ->delete('Tagging t')
#        ->where('t.taggable_model = ?', 'Intervention')
#        ->andWhere('t.taggable_id = ?', $inter)
#        ->execute();
#      $query = Doctrine_Query::create()
#        ->delete('Intervention i')
#        ->where('i.id = ?', $inter);
#      if (! $query->execute()) {
#        print 'Suppression impossible de l\'intervention N°'.$inter."\n";
#        return;
#      }
#    }

  }
}


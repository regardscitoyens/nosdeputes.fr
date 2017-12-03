<?php

class mergeReunionsJointesTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'merge';
    $this->name = 'ReunionsJointes';
    $this->briefDescription = "Fusionner proprement une réunion conjointe dans une autre (déplace les présences de la première dans la seconde et en supprime les interventions désirées pour une didascalie pointant un lien vers l'autre";
    $this->addArgument('badid', sfCommandArgument::REQUIRED, 'Id de la séance à expurger des doublons');
    $this->addArgument('gdid', sfCommandArgument::REQUIRED, "Id de la séance d'accueil");
    $this->addArgument('firstinter', sfCommandArgument::REQUIRED, "Id de la première intervention à supprimer ou none");
    $this->addArgument('lastinter', sfCommandArgument::REQUIRED, "Id de la dernière intervention à supprimer");
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $this->configuration->loadHelpers(array('Url'));
    $manager = new sfDatabaseManager($this->configuration);
    sfContext::createInstance($this->configuration);

    $badid = $arguments['badid'];
    $badseance = Doctrine::getTable('Seance')->find($badid);
    if (!$badseance) {
      print "Séance ".$badid." inexistante\n";
      return;
    }
    $badseanceurl = preg_replace('#http://(symfony/)+#', '/', url_for("@interventions_seance?seance=".$badid, 'absolute=false'));
    $badseancefurl = trim(sfConfig::get('app_base_url'), "/").$badseanceurl;

    $gdid = $arguments['gdid'];
    $gdseance = Doctrine::getTable('Seance')->find($gdid);
    if (!$gdseance) {
      print "Séance ".$gdid." inexistante\n";
      return;
    }
    $gdseanceurl = preg_replace('#http://(symfony/)+#', '/', url_for("@interventions_seance?seance=".$gdid, 'absolute=false'));
    $gdseancefurl = trim(sfConfig::get('app_base_url'), "/").$gdseanceurl;

  if ($arguments['firstinter'] !== "none") {
    $firstinter = Doctrine::getTable('Intervention')->find($arguments['firstinter']);
    if (!$firstinter) {
      print "Intervention ".$arguments['firstinter']." inexistante\n";
      return;
    }
    if ($firstinter->seance_id != $badid) {
      print "L'intervention ".$arguments['firstinter']." n'appartient pas à la séance ".$badid."\n";
      return;
    }

    $lastinter = Doctrine::getTable('Intervention')->find($arguments['lastinter']);
    if (!$lastinter) {
      print "Intervention ".$arguments['lastinter']." inexistante\n";
      return;
    }
    if ($lastinter->seance_id != $badid) {
      print "L'intervention ".$arguments['lastinter']." n'appartient pas à la séance ".$badid."\n";
      return;
    }

    # Check if interventions to be removed have comments
    if (Doctrine_Query::create()->select('c.id')->from('Commentaire c, Intervention i')->where('i.id = c.object_id')->andWhere('c.object_type = "Intervention"')->andWhere('i.seance_id = ?', $badid)->andWhere('i.timestamp >= ?', $firstinter->timestamp)->andWhere('i.timestamp <= ?', $lastinter->timestamp)->fetchOne()) {
      print ": Un ou plusieurs commentaires sont associés aux interventions à supprimer de la séance ".$badseancefurl.", veillez à corriger cela en premier lieu\n";
      return;
    }

    # Replace first intervention with didascalie pointing link to good seance
    echo "-> creating metadata didascalie on bad seance: ".$badseancefurl."#inter_".$firstinter->md5."\n";
    $firstinter->setAsDidascalie();
    $firstinter->setIntervention('<p>Le compte-rendu de cette réunion conjointe est lisible à l\'adresse suivante : <a href="'.$gdseanceurl.'">'.$gdseancefurl.'</a>.</p>');
    $firstinter->save();

    # Remove interventions from bad seance
    echo $firstinter->timestamp." -> ".$lastinter->timestamp."\n";
    echo "-> Removing duplicate interventions from bad seance... ";

    foreach (Doctrine_Query::create()->select('id, timestamp')->from('Intervention')->where('seance_id = ?', $badid)->orderBy('timestamp')->fetchArray() as $intervention) {
      $inter = $intervention['id'];
      $interts = $intervention['timestamp'];
      if ($interts <= $firstinter->timestamp || $interts > $lastinter->timestamp)
        continue;
      echo $inter." / ";
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
    echo "\n";
  }

    # Merge présences bad seance into good seance
    echo "-> Move présences from bad seance to good seance...";
    foreach (Doctrine_Query::create()->select('id, parlementaire_id, nb_preuves')->from('Presence')->where('seance_id = ?', $badid)->fetchArray() as $presence) {
      $pres = $presence['id'];
      $parl = $presence['parlementaire_id'];
      # check if presence existante in good seance
      $existing = Doctrine_Query::create()->select('id, nb_preuves')->from('Presence')->where('seance_id = ?', $gdid)->andWhere('parlementaire_id = ?', $parl)->fetchOne();
      if ($existing) {
        # add to it all preuves from bad seance...
        $query = Doctrine_Query::create()
          ->update('PreuvePresence')
          ->set('presence_id', $existing['id'])
          ->where('presence_id = ?', $pres)
          ->execute();
        echo "\nMerging existing présence for parl #".$parl.", ";
        # ...update nb preuves présence for gd one...
        $query = Doctrine_Query::create()
          ->update('Presence')
          ->set('nb_preuves', $presence['nb_preuves'] + $existing['nb_preuves'])
          ->where('id = ?', $existing['id'])
          ->execute();
        echo $presence['nb_preuves']." preuves added to ".$existing['nb_preuves']." already existing ones, ";
        # ...and remove bad presence
        $query = Doctrine_Query::create()
          ->delete('Presence')
          ->where('id = ?', $pres);
        if (! $query->execute()) {
          print 'Suppression impossible de la présence N°'.$pres."\n";
          return;
        }
        echo "old presence removed.";
      } else {
        # if not, just change seance_id into bad presence for gdseance->id
        $query = Doctrine_Query::create()
          ->update('Presence')
          ->set('seance_id', $gdid)
          ->where('id = ?', $pres)
          ->execute();
        echo "\nMoved présence for parl #".$parl." to good seance.";
      }
    }
    echo "\n";
  }
}


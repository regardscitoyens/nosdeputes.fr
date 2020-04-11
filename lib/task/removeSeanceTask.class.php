<?php

class removeSeanceTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'Seance';
    $this->briefDescription = 'Supprimer proprement une séance et ses dépendances';
    $this->addArgument('id', sfCommandArgument::REQUIRED, 'Id de la séance à supprimer'); 
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
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
    if (Doctrine::getTable('Commentaire')->createQuery('c')->where('c.object_type = ?', 'Intervention')->andWhere('c.object_id = ?', $id)->fetchOne()) {
      print "Un ou plusieurs commentaires sont associés à cette séance, veillez à corriger cela en premier lieu\n";
      return;
    }

    print " - Gère les présences\n";
    foreach (Doctrine_Query::create()->select('id')->from('Presence')->where('seance_id = ?', $id)->fetchArray() as $presence) {
      $pres = $presence['id'];
      print $pres."//";
      $query = Doctrine_Query::create()
        ->delete('PreuvePresence p')
        ->where('p.presence_id = ?', $pres)
        ->execute();
      $query = Doctrine_Query::create()
        ->delete('Presence p')
        ->where('p.id = ?', $pres);
      if (! $query->execute()) {
        print 'Suppression impossible de la présence N°'.$pres."\n";
        return;
      }
    }

    print "\n - Gère les interventions et leurs tags\n";
    foreach (Doctrine_Query::create()->select('id')->from('Intervention')->where('seance_id = ?', $id)->fetchArray() as $intervention) {
      $inter = $intervention['id'];
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


<?php

class removeCommentairesSeanceTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'CommentairesSeance';
    $this->briefDescription = 'Désattacher et afficher les commentaires d\'une séance';
    $this->addArgument('id', sfCommandArgument::REQUIRED, 'Id de la séance à nettoyer');
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
    $comments = Doctrine_Query::create()->select('c.id as id, i.parlementaire_id as dep, i.personnalite_id as pers, i.fonction as fct, i.intervention as txt, i.timestamp as ts')->from('Commentaire c, Intervention i')->where('i.id = c.object_id')->andWhere('c.object_type = "Intervention"')->andWhere('i.seance_id = ?', $id)->fetchArray();
    if (!$comments) {
      print ": aucun commentaire n'est associé à cette séance\n";
      return;
    }
    foreach ($comments as $c) {
      print $c['id']." : (".$c['dep']."/".$c['pers'].", ".$c['fct'].") ".$c['txt']."(".$c['ts'].")\n";
      Doctrine_Query::create()
        ->update('Commentaire')
        ->set('object_id', 0)
        ->where('id = ?', $c['id'])
        ->execute();
    }
  }
}


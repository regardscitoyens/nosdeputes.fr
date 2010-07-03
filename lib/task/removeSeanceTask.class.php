<?php

class removeSeanceTask extends sfBaseTask {
  protected $transaction = false;
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'Seance';
    $this->briefDescription = 'Supprimer proprement une séance et ses dépendances';
    $this->addArgument('id', sfCommandArgument::REQUIRED, 'Id de la séance à supprimer'); 
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  function __destruct() {
    if ($this->transaction)
      echo $this->transaction->rollback();
    echo "deletet\n";
  }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);


    $this->conn = Doctrine_Manager::connection();

    try {
      $this->conn->beginTransaction();
      $this->executeIntern($arguments, $option);
      $this->conn->commit();
    }catch(Exception $e) {
      print "Rollback due to ".$e->getMessage()."\n";
      $this->conn->rollback();
    }
  }

  private function executeIntern($arguments = array(), $options = array()) {
    $seance = Doctrine::getTable('Seance')->find($arguments['id']);
    if (!$seance) {
      print "Séance inexistante\n";
      throw new Exception('Séance inexistante');
      return ;
    }
    $id = $seance->id;
    print "Séance n° $id :\n";


    if (Doctrine::getTable('Commentaire')->createQuery('c')->where('c.object_type = ?', 'Intervention')->andWhere('c.object_id = ?', $id)->fetchOne()) {
      print "Un ou plusieurs commentaires sont associés à cette séance, veillez à corriger cela en premier lieu\n";
      throw new Exception('commentaire');
      return;
    }

    print " - Gère les présences\n";
    $presences = Doctrine_Query::create($this->conn)->select('id')->from('Presence')->where('seance_id = ?', $id)->fetchArray();
    print "    ".count($presences)." à supprimer\n";
    if (count($presences)) {
      $query = Doctrine_Query::create($this->conn)
	->delete('PreuvePresence p')
	->whereIn('p.presence_id IN', $presences);
      
      $query->execute();
      
      $query = Doctrine_Query::create($this->conn)
	->delete('Presence p')
      ->whereIn('p.id', $presences);
      
      $query->execute();

    }
    print "    DONE\n";

    print " - Gère les interventions et leurs tags\n";
    $interventions = Doctrine_Query::create($this->conn)->select('id')->from('Intervention')->where('seance_id = ?', $id)->fetchArray();
    print "    ".count($interventions)." à supprimer\n";
    if (count($interventions)) {
      $query = Doctrine_Query::create($this->conn)
	->delete('Tagging t')
	->whereIn('t.taggable_id', $interventions)
	->andWhere('t.taggable_model = ?', 'Intervention');
      $query->execute();
      
      $query = Doctrine_Query::create($this->conn)
	->delete('Intervention i')
	->whereIn('i.id', $interventions);
      $query->execute();

    }
    print "    DONE\n";

    //    throw new Exception('test');

    $query = Doctrine_Query::create($this->conn)
      ->delete('Seance s')
      ->where('s.id = ?', $id);
    $query->execute();

    print "Séance ".$id." supprimée avec ses dépendances\n";
  }
}


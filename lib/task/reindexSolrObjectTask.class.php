<?php

class reindexSolrObjectTask extends sfBaseTask {

  protected function configure() {
    $this->namespace = 'reindex';
    $this->name = 'SolrObject';
    $this->briefDescription = 'Réindexe un objet ou supprime son index si non-existant ; usage : php symfony reindex:SolrObject object_class object_id';
    $this->addArgument('class', sfCommandArgument::REQUIRED, 'Classe de l\'objet');
    $this->addArgument('id', sfCommandArgument::REQUIRED, 'ID de l\'objet');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $class = $arguments['class'];
    if (!preg_match('/^(Commentaire|Intervention|Amendement|QuestionEcrite|Section|Organisme|Texteloi|Parlementaire)$/', $class)) {
      echo "ERREUR : $class n'est pas une classe d'objet indexé dans Solr\n";
      return;
    }
    $obj = Doctrine::getTable($class)->find($id);
    if (!$obj) {
      $json = new stdClass();
      $json->id = $class.'/'.$id;
      SolrCommands::getInstance()->addCommand('DELETE', $json);
    } else {
      $obj->save();
    }
  }

}


<?php

class taggableCleanTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_NONE, 'Display more output', NULL),
      // add your own options here
    ));

    $this->namespace        = 'taggable';
    $this->name             = 'clean';
    $this->briefDescription = 'deletes orphaned/unused Tag objects';
    $this->detailedDescription = <<<EOF
The [taggable:clean|INFO] task {$this->briefDescription}.
Call it with:

  [php symfony taggable:clean|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $deleted = PluginTagTable::purgeOrphans();
    if ($options['verbose'])
    {
      $count = count($deleted);
      echo "deleted $count orphan tags.\n";      
    }
  }
}

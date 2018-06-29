<?php

class loadScrutinsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'Scrutins';
    $this->briefDescription = 'Load Scrutin data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/scrutin/scrutin/';
    $backupdir = dirname(__FILE__).'/../../batch/scrutin/loaded/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      foreach (scandir($dir) as $file) {
        if (!preg_match('/\.json$/', $file)) {
          continue;
        }

        echo "$dir$file\n";
        $json = file_get_contents($dir . $file);
        $data = json_decode($json);

        if (!$data) {
          echo "ERROR json : $data\n";
          continue;
        }

        $scrutin = Doctrine::getTable('Scrutin')->findOneByNumero($data->numero);
        if (!$scrutin) {
          $scrutin = new Scrutin();
          $scrutin->setNumero($data->numero);
        }

        try {
          $scrutin->setSeance($data->seance);
          $scrutin->setDemandeur($data->demandeur);
          $scrutin->setTitre($data->titre);
          $scrutin->setType($data->type);
          $scrutin->setStats($data->sort,
                             $data->nombre_votants,
                             $data->nombre_pours,
                             $data->nombre_contres,
                             $data->nombre_abstentions);

        } catch(Exception $e) {
          echo "ERREUR $file (scrutin) : {$e->getMessage()}\n";
          continue;
        }

        $scrutin->save();
        $scrutin->setVotes($data->parlementaires);
        $scrutin->free();

        rename($dir . $file, $backupdir . $file);
      }
    }
  }
}

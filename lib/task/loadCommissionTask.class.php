<?php

class loadCommissionTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'Commission';
    $this->briefDescription = 'Load Commission data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->addOption('verbose', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 0);
  }

  protected function execute($arguments = array(), $options = array())
  {
    $dir = dirname(__FILE__).'/../../batch/commission/out/';
    $backupdir = dirname(__FILE__).'/../../batch/commission/loaded/';
    $manager = new sfDatabaseManager($this->configuration);

    $verbose = $options['verbose'];

    if (is_dir($dir)) {
      foreach (scandir($dir) as $file) {
          if (preg_match('/^\./', $file))
            continue;
          echo "$dir$file\n";
          $first = 1;
          foreach(file($dir.$file) as $line) {
            $json = json_decode($line);
            if (!$json || !$json->intervention || !$json->date || !$json->commission || !$json->source) {
              echo "ERROR json : ";
              echo $line;
              echo "\n";
              continue;
            }

            if (strlen($json->commission) > 255) {
              $json->commission = preg_replace('/ \S+$/', '', substr($json->commission, 0, 255));
            }

            if ($first) { #On teste si la séance existe déjà.
              $first = 0;
              $seance = Doctrine::getTable('Seance')->getFromSeanceArgs('commission', $json->date, $json->heure, $json->session, $json->commission);
              if ($seance) {
                try {
                  $seance->deleteInterventions();
                }catch(Exception $e) {
                  echo "ERROR: Impossible de supprimer la séance ".$seance->id." (".$e->getMessage().")\n";
                  continue 2;
                }
              }
            }

            $id = md5($json->intervention.$json->date.$json->heure.$json->commission.$json->timestamp);
            $intervention = Doctrine::getTable('Intervention')->findOneByMd5($id);
            if(!$intervention) {
              if ($verbose)
                echo "Create intervention for $id\n";
              $intervention = new Intervention();
              $intervention->md5 = $id;
              $intervention->setIntervention($json->intervention);
              $intervention->setSeance('commission', $json->date, $json->heure, $json->session, $json->commission);
              $intervention->date = $json->date;
              $intervention->setSource($json->source);
              $intervention->setTimestamp($json->timestamp);
            }
            if ($json->intervenant) {
              if ($verbose)
                echo "Set Intervenant for $id (".$json->intervenant.")\n";
              $intervention->setPersonnaliteByNom($json->intervenant, $json->fonction);
              if ($verbose)
                echo "Parlementaire ".$intervention->parlementaire_id." set for $id\n";
            }
            $intervention->save();
            $intervention->free();
          }
          rename($dir.$file, $backupdir.$file);
        }
    }
  }
}

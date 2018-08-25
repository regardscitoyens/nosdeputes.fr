<?php

class cleanSenateursTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'clean';
    $this->name = 'Senateurs';
    $this->briefDescription = 'Clean site Senateurs';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array())
  {
    $manager = new sfDatabaseManager($this->configuration);

    $query = Doctrine::getTable('Parlementaire')->createQuery('p');
    foreach ($query->execute() as $parl) {
        $replace = array();
        foreach(unserialize($parl->sites_web)  as $site) {
            if ($site && !preg_match('/^http...(www\.)?senat\.fr/', $site)) {
                $replace[] = $site;
            }
        }
        $parl->setSitesWeb($replace);
	    $parl->save();
    }
  }
}

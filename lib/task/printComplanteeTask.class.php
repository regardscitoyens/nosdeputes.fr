<?php

class printComplanteeTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'print';
    $this->name = 'Complantee';
    $this->briefDescription = 'Load Commission data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/commission/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  if (preg_match('/^\./', $file))
	    continue;
	  echo "$dir$file\n";
	}
        closedir($dh);
      }
    }
  }
}

<?php

class SolrCommands 
{
  public static function getFileCommands() {
    umask(0000);
    if (!file_exists(sfConfig::get('sf_log_dir').'/solr/')) {
      mkdir (sfConfig::get('sf_log_dir').'/solr/');
    }
    if (!file_exists(sfConfig::get('sf_log_dir').'/solr/commands.log'))
      touch(sfConfig::get('sf_log_dir').'/solr/commands.log');
    return sfConfig::get('sf_log_dir').'/solr/commands.log';
  }

  protected $semaphore = null;
  protected $file = null; 

  protected static $instance = null;
  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new SolrCommands();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->semaphore = null;
  }

  public function __destruct() {
    if ($this->semaphore) {
      sem_remove($this->semaphore);
      $this->semaphore = null;
    }
  }

  private static function getSemId() {
    self::getFileCommands();
    $semfile = sfConfig::get('sf_log_dir')."/solr/SolrSem.id";
    if (!file_exists($semfile)) {
      touch($semfile);
    }
    $id = ftok($semfile, 's');
    return $id;
  }

  private function protect() {
#    if (! $this->semaphore) {
      $this->semaphore = sem_get(self::getSemId(), 1, 0666, -1);
#    }
    sem_acquire($this->semaphore);
  }

  private function unprotect() {
    sem_release($this->semaphore);
  }

  public function addCommand($status, $json) {
    $this->protect();
    if (! $this->file) {
      $this->file = fopen($this->getFileCommands(), 'a+');
    }
    $str = $status.' : '.json_encode($json)."\n";
    fwrite($this->file, $str, strlen($str));
    fclose($this->file);
    $this->file = null;
    $this->unprotect();
  }

  public  function getCommandContent() {
    $lockfile = $this->getFileCommands().'.lock';
    if (file_exists($lockfile)) {
      return $lockfile;
    }
    $this->protect();
    if ($this->file) {
      fclose($this->file);
      $this->file = null;
    }
    if (!file_exists($this->getFileCommands()))
      touch($this->getFileCommands());
    rename($this->getFileCommands(), $lockfile);
    $this->unprotect();
    return $lockfile;
  }
  public  function releaseCommandContent() {
    $this->protect();
    unlink($this->getFileCommands().'.lock');
    $this->unprotect();
  }
}


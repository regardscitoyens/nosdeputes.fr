<?php

class SolrCommands 
{
  public static function getFileCommands() {
    umask(0002);
    if (!file_exists(sfConfig::get('sf_log_dir').'/solr/')) {
      mkdir (sfConfig::get('sf_log_dir').'/solr/');
    }
    return sfConfig::get('sf_log_dir').'/solr/commands.log';
  }

  protected $semaphore = null;
  protected $file = null; 

  protected static $instance = null;
  protected static $semaphore_id = "99999910823498202340982340982340981678";
  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new SolrCommands();
    }
    return self::$instance;
  }

  public function __construct() {
    $this->semaphore = sem_get(self::$semaphore_id);
  }

  public function __destruct() {
    sem_remove($this->semaphore);
    $this->semaphore = null;
  }

  public function addCommand($status, $json) {
    sem_acquire($this->semaphore);
    if (! $this->file) {
      $this->file = fopen($this->getFileCommands(), 'a+');
    }
    $str = $status.' : '.json_encode($json)."\n";
    fwrite($this->file, $str, strlen($str));
    sem_release($this->semaphore);
  }

  public  function getCommandContent() {
    $lockfile = $this->getFileCommands().'.lock';
    if (file_exists($lockfile)) {
      return $lockfile;
    }
    sem_acquire($this->semaphore);
    if ($this->file) {
      fclose($this->file);
      $this->file = null;
    }
    if (!file_exists($this->getFileCommands()))
      touch($this->getFileCommands());
    rename($this->getFileCommands(), $lockfile);
    sem_release($this->semaphore);
    return $lockfile;
  }
  public  function releaseCommandContent() {
    sem_acquire($this->semaphore);
    unlink($this->getFileCommands().'.lock');
    sem_release($this->semaphore);
  }
}


<?php

class SolrCommands 
{
  public static function getFileCommands() {
    return sfConfig::get('sf_log_dir').'/solr/commands.log';
  }

  protected static $semaphore = null;
  protected static $file = null; 

  protected static function getSemaphore() {
    if (! self::$semaphore) {
      self::$semaphore = sem_get(rand());
    }
    return self::$semaphore;
  }

  public static function addCommand($status, $json) {
    sem_acquire(self::getSemaphore());
    if (! self::$file) {
      self::$file = fopen(self::getFileCommands(), 'w');
    }
    $str = $status.' : '.json_encode($json);
    fwrite(self::$file, $str, strlen($str));
    sem_release(self::getSemaphore());
  }

  public static function getCommandContent() {
    $lockfile = self::getFileCommands().'.lock';
    if (file_exists($lockfile)) {
      return $lockfile;
    }
    sem_acquire(self::getSemaphore());
    if (!self::$file) {
      touch($lockfile);
      sem_release(self::getSemaphore());
      return $lockfile;
    }
    fclose(self::$file);
    self::$file = null;
    rename(self::getFileCommands(), self::getFileCommands().'.lock');
    sem_release(self::getSemaphore());
    return $lockfile;
  }
  public static function releaseCommandContent() {
    sem_acquire(self::getSemaphore());
    unlink(self::getFileCommands().'.lock');
    sem_release(self::getSemaphore());
  }
}


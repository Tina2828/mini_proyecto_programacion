<?php

require_once __DIR__.'/DatabaseConnection.php';

defined('APPLICATION_PATH') or define('APPLICATION_PATH', __DIR__.'/../app');

class Application {

  public function __construct(){
    $this->connection = new DatabaseConnection();
    $this->connection->connect();
    $this->loadControllers();
  }

  public function loadControllers() {
    $controllersDir = APPLICATION_PATH.'/controllers';
    $files = scandir($controllersDir);
    foreach ($files as $file) {
      if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
        continue;
      }

      require_once $controllersDir.'/'.$file;
    }
  }

  public function run(){
    Log::info('Application is running');
  }

  public function __destruct(){
    if ($this->connection) {
      $this->connection->disconnect();
    }
  }

  private $connection = null;
}

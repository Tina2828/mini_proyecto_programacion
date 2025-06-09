<?php

require_once __DIR__.'/Routes.php';
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
    require_once __DIR__."/../config/routes.php";

    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    $pathname = explode('?', $uri)[0];
    $pathname = trim($pathname, '/');

    Log::info("Request URI: $pathname, Method: $method");
    $route = Routes::match($method,$pathname);

    if(!$route){
      Log::error("No route found for $method $pathname");
      http_response_code(404);
      echo "404 Not Found";
      return;
    }

    $handler = $route['handler'];
    $values = explode("@", $handler);

    Log::debug($values);

    $controllerName = $values[0];
    $actionName = $values[1];

    if (!class_exists($controllerName)) {
      Log::error("Controller $controllerName not found");
      http_response_code(404);
      echo "404 Not Found";
      return;
    }

    $controller = new $controllerName($this->connection);
    if (!method_exists($controller, $actionName)) {
      Log::error("Action $actionName not found in controller $controllerName");
      http_response_code(404);
      echo "404 Not Found";
      return;
    }
    Log::info("Executing $controllerName@$actionName");
    $controller->$actionName();
  }

  public function __destruct(){
    if ($this->connection) {
      $this->connection->disconnect();
    }
  }

  private $connection = null;
}

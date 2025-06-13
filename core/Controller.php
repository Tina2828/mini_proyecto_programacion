<?php

require_once __DIR__."/Views.php";
require_once __DIR__.'/DatabaseConnection.php';

class Controller {
  public function __construct(DatabaseConnection $connection) {
    $this->connection = $connection;
  }

  public function db() {
    return $this->connection;
  }

  public function query(){
    return (object) $_GET;
  }

  public function body(){
    return (object) $_POST;
  }

  public function redirect($pathname){
    header("Location: {$pathname}");
  }

  /**
   * Renders a view template with the provided data.
   * @param string $template The name of the template to render.
   * @param array $data An associative array of data to pass to the view.
   * @return void
   */
  public function render($template, $data = []) {
    $view = new Views();
    $content = $view->render($template, $data);
    echo $content;
  }

  protected $connection;
}


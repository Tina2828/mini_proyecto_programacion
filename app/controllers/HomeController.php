<?php
require_once __DIR__.'/../../core/Controller.php';

class HomeController extends Controller {
  public function index() {
    $this->render('home/index', [
      'title' => 'Home',
      'message' => 'Welcome to the home page!'
    ]);
  }
}

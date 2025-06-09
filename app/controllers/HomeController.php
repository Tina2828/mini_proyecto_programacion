<?php
require_once __DIR__.'/../../core/Controller.php';
require_once __DIR__."/../models/TareasModel.php";
require_once __DIR__."/../models/CategoriasModel.php";

class HomeController extends Controller {

  public function index() {
    $query = (new TareasModel($this->connection))
      ->query()
      ->select(["tareas.*", "categories.name as categoria_nombre"])
      ->join('categories', 'categories.id = tareas.category_id')
      ->limit(20);

    $tareas = $query->get();

    $this->render('home/index', [ "tareas" => $tareas ]);
  }

  public function destroy_tarea() {
    $query = $this->query();
    TareasModel::destroy($query->id);
    $this->redirect('/tareas');
  }

  public function create_tarea() {
    $tareasModel = new TareasModel($this->connection);
    $this->redirect('/tareas');
  }

  public function edit_categoria() {
    $categoriasModel = new CategoriasModel($this->connection);
    $this->redirect('/tareas');
  }
}

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

  public function destroyTarea() {
    $query = $this->query();
    TareasModel::destroy($query->id);
    $this->redirect('/tareas');
  }

  public function createTarea() {
    $schema = $this->schemaTarea();
    $body = (array) $this->body();
    $valid = $schema->validate($body);

    if(!$valid) {
      $this->render("form/new.phtml", ["errors" => $schema->errors(), "values" => $body]);
      return;
    }

    $tareasModel = new TareasModel($this->connection);
    $tareasModel
       ->query()
       ->insert($body);

    $this->redirect("/tareas");
  }

  public function updateTarea() {
    $categoriasModel = new CategoriasModel($this->connection);
    $this->redirect('/tareas');
  }

  private function schemaTarea(): ValidatorRule {
    $schema = new Validator([
      "title" => Validator::string()
          ->trim()
          ->required()
          ->min(3)
          ->max(250),
      "due_date" => Validator::datetime()
          ->required()
          ->format("Y-m-d H:i:s")
          ->min(new DateTime()),
       "description" => Validator::string()
          ->trim()
          ->required()
          ->min(8)
          ->max(500),
    ]);

    return $schema;
  }
}

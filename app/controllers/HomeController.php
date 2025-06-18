<?php
require_once __DIR__.'/../../core/Controller.php';
require_once __DIR__."/../models/TareasModel.php";
require_once __DIR__."/../models/CategoriasModel.php";
require_once __DIR__."/../../core/Validator/index.php";

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
    TareasModel::destroy($this->connection, $query->id);
    $this->redirect('/tareas');
  }

  public function newTarea(){
     $this->render("form/tarea", ["action" => "/tareas/create"]);
  }

  public function editTarea() {
    $query = $this->query();

    $tarea = (new TareasModel($this->connection))
      ->query()
      ->select(["tareas.*"])
      ->where("tareas.id", "=", $query->id)
      ->first();

    $this->render("form/tarea",["values" => (array) $tarea]);
  }

  public function createTarea() {
    $schema = $this->schemaTarea();
    $body = (array) $this->body();
    $valid = $schema->validate($body);

    if(!$valid) {
      $this->render("form/tarea", ["errors" => $schema->errors(), "values" => $body, "action" => "/tareas/create"]);
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
       "priority" => Validator::string()
         ->trim()
         ->required()
         ->enum(['baja', 'media', 'alta']),
    ]);

    return $schema;
  }
}

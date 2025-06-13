<?php

require_once __DIR__."/../../core/Model.php";

class TareasModel extends Model {
  protected $tableName = "tareas";

  public static function destroy(DatabaseConnection $connection, int $id) {
    $model = new self($connection);
    $builder = $model->query();
    $builder->where("id", "=", $id);
    $builder->delete();
    return true;
  }
}

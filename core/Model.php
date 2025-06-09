<?php

require_once __DIR__."/DatabaseQueryBuilder.php";

class Model {

  public function __construct($db) {
    $this->db = $db;
  }

  public function query() {
    $builder = new DatabaseQueryBuilder($this->db);
    $builder->table($this->tableName);
    return $builder;
  }


  protected $tableName;
  protected $db;
}

<?php
require_once __DIR__."/ValidatorRule.php";

class ValidatorDateTime implements ValidatorRule {

  public function __construct(){
    $this->rules = [];
    $this->errors = [];
  }

  public function validate($value): bool {

    foreach($this->rules as $rule) {
      $rule($value);
    }

    return empty($this->errors);
  }

  public function errors() {
    return $this->errors;
  }

  public function required() {
    $this->rules[] = function(string|DateTime|null &$val) {
      if($val) {
        return;
      }

      $this->errors[] = "El valor es requerido";
    };

    return $this;
  }

  public function format($format) {
    $ref = &$this;

    $this->rules[] = function(string|DateTime|null &$val) use($format, &$ref) {
      if($val == null) {
        return;
      }

      if($val instanceof DateTime) {
        return;
      }

      $val =  DateTime::createFromFormat($format, $val);
      if($val){
        return;
      }

      $ref->errors[] = "El valor obtenido no de fecha";
    };

    return $this;
  }

  public function min(DateTime $date) {
    $ref = &$this;

    $this->rules[] = function(null|DateTime $val) use($date, &$ref) {
      if($val == null) {
        return;
      }

      if($date->getTimestamp() <= $val->getTimestamp()){
        return;
      }

      $ref->errors[] = "El valor obtenido no de fecha";
    };

    return $this;
  }

  public function max(DateTime $date) {
    $ref = &$this;

    $this->rules[] = function(null|DateTime $val) use($date, &$ref) {
      if($val == null) {
        return;
      }

      if($date->getTimestamp() >= $val->getTimestamp()){
        return;
      }

      $ref->errors[] = "El valor obtenido no de fecha";
    };

    return $this;
  }


  private $errors = null;
  private $rules = null;
}

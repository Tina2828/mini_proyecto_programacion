<?php
require_once __DIR__."/ValidatorRule.php";

class ValidatorString implements ValidatorRule {

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

  public function enum(array $values): self  {
    $ref = &$this;

    $this->rules[] = function(null|string $val) use($values, &$ref) {
      if($val == null) {
        return;
      }

      if(in_array($val, $values)) {
        return;
      }

      $ref->errors[] = "El valor no esta en los valores permitido";
    };

    return $this;
  }

  public function trim(): self {
    $this->rules[] = function(string|null &$val) {
      if(!$val){
        return;
      }

      $val = trim($val);
    };

    return $this;
  }

  public function max(int $length): self {
    $ref = &$this;

    $this->rules[] = function (null|string $val) use($length, &$ref) {
      if($val == null){
        return;
      }

      if(strlen($val) <= $length) {
        return;
      }

      $ref->errors[] = "No puede tener mas de {$length} caracteres";
    };

    return $this;
  }

  public function min(int $length): self {
    $ref = &$this;

    $this->rules[] = function (null|string $val) use($length, &$ref) {
      if($val == null){
        return;
      }

      if(strlen($val) >= $length) {
        return;
      }

      $ref->errors[] = "Debe tener al menos {$length} caracteres";
    };

    return $this;
  }

  private $rules = null;
  private $errors = null;
}

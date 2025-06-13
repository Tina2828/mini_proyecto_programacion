<?php

require_once __DIR__."/ValidatorRule.php";

class Validator implements ValidatorRule {

  public function __construct(array $rules) {
    foreach($rules as $key => $rule) {
      if(!($rule instanceof ValidatorRule)) {
        throw new Exception("Hay una propiedad llamada {$key} que no esta implementando ValidatorRule");
      }
    }

    $this->rules = $rules;
  }

  public function validate($value): bool {
    $rules = $this->rules;

    foreach($rules as $key => $rule) {
      $valid = $rule->validate($value[$key]);

      if($valid){
        continue;
      }

      $this->errors[$key] = $rule->errors();
   }

   return empty($this->errors);
  }

  public function errors() {
    return $this->errors();
  }

  static public function string() {

  }

  static public function number() {

  }

  static public function datetime() {

  }


  private $errors = null;
  private $rules = null;
}

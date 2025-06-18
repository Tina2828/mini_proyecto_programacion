<?php

require_once __DIR__."/ValidatorRule.php";
require_once __DIR__."/ValidatorString.php";
require_once __DIR__."/ValidatorDateTime.php";

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
      $curr_val = empty($value[$key]) ? null : $value[$key];
      $valid = $rule->validate($curr_val);

      if($valid){
        continue;
      }

      $this->errors[$key] = $rule->errors();
   }

   return empty($this->errors);
  }

  public function errors() {
    return $this->errors;
  }

  static public function string() {
    return new ValidatorString();
  }

  static public function datetime() {
    return new ValidatorDateTime();
  }

  private $errors = null;
  private $rules = null;
}

<?php
require_once __DIR__."/ValidatorRule.php";
require_once __DIR__."/../DatabaseQueryBuilder.php";

class ValidatorNumber implements ValidatorRule {

    public function __construct() {
        $this->rules = [];
        $this->errors = [];
    }

    public function exists(DatabaseConnection $connection, string $table, string $field = "id") {
        $this->rules[] = function($val) use(&$connection, $field, $table) {
          $curr = (new DatabaseQueryBuilder($connection))
             ->table($table)
             ->where($field, "=", $val)
             ->first();

           if($curr){
             return;
           }

           $this->errors[] = "El valor es requerido";
        };

        return $this;
    }

    public function validate($value): bool {
        if (is_string($value)) {
            $value = is_numeric($value) ? $value + 0 : $value;
        }

        foreach ($this->rules as $rule) {
            $rule($value);
        }

        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function required(): self {
        $this->rules[] = function($val) {
            if ($val === null || $val === '') {
                $this->errors[] = "El valor es requerido";
            }
        };
        return $this;
    }

    public function min(float $min): self {
        $this->rules[] = function($val) use ($min) {
            if ($val === null) return;

            if ($val < $min) {
                $this->errors[] = "El valor debe ser mayor o igual a {$min}";
            }
        };
        return $this;
    }

    public function max(float $max): self {
        $this->rules[] = function($val) use ($max) {
            if ($val === null) return;

            if ($val > $max) {
                $this->errors[] = "El valor debe ser menor o igual a {$max}";
            }
        };
        return $this;
    }

    public function between(float $min, float $max): self {
        $this->rules[] = function($val) use ($min, $max) {
            if ($val === null) return;

            if ($val < $min || $val > $max) {
                $this->errors[] = "El valor debe estar entre {$min} y {$max}";
            }
        };
        return $this;
    }

    public function positive(): self {
        $this->rules[] = function($val) {
            if ($val === null) return;

            if ($val <= 0) {
                $this->errors[] = "El valor debe ser positivo";
            }
        };
        return $this;
    }

    public function negative(): self {
        $this->rules[] = function($val) {
            if ($val === null) return;

            if ($val >= 0) {
                $this->errors[] = "El valor debe ser negativo";
            }
        };
        return $this;
    }

    public function integer(): self {
        $this->rules[] = function($val) {
          if ($val === null) {
            return;
          }
          if (!is_int($val)){
             $this->errors[] = "El valor debe ser un número entero";
          }
        };


        return $this;
    }

    public function decimal(int $decimals): self {
        $this->rules[] = function($val) use ($decimals) {
            if ($val === null) return;

            $parts = explode('.', (string)$val);
            if (count($parts) === 2 && strlen($parts[1]) > $decimals) {
                $this->errors[] = "El número no puede tener más de {$decimals} decimales";
            }
        };
        return $this;
    }

    private $rules = null;
    private $errors = null;
}

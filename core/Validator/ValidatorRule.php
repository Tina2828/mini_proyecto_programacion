<?php

interface ValidatorRule {
  public function validate($value): bool;
  public function errors();
}

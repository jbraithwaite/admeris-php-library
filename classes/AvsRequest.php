<?php namespace Admeris;

class AvsRequest{

  private $code = null;

  public function __construct($code)
  {
    $this->code = $code;
  }
}
<?php namespace Admeris;

class Cvv2Request{

  private $code = null;

  public function __construct($code)
  {
    $this->code = $code;
  }
}

<?php namespace Admeris;

class Cvv2Response extends admeris_base_model{

  private $code;
  private $message;

  public function __construct($code, $message)
  {
    $this->code = $code;
    $this->message = $message;
  }

  function _get_code()
  {
    return $this->code;
  }

  function _get_message()
  {
    return $this->message;
  }
}
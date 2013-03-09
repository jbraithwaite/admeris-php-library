<?php namespace Admeris;

use Admeris\Base;

class VerificationRequest extends Base {

  private $avsRequest;
  private $cvv2Request;

  public function __construct($avsRequest, $cvv2Request) {
    $this->avsRequest = $avsRequest;
    $this->cvv2Request = $cvv2Request;
  }

  function _get_avsRequest() {return $this->avsRequest;}
  function _get_cvv2Request() {return $this->cvv2Request;}

  function isAvsEnabled()
  {
    return $this->avsRequest !== NULL;
  }

  function isCvv2Enabled(){
    return $this->cvv2Request !== NULL;
  }
}
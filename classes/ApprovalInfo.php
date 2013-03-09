<?php namespace Admeris;

use Admeris\Base;

class ApprovalInfo extends Base {

  private $authorizedAmount = 0;
  private $approvalCode = null;
  private $traceNumber = null;
  private $referenceNumber = null;

  public function __construct($authorizedAmount, $approvalCode, $traceNumber, $referenceNumber)
  {
    $this->authorizedAmount = $authorizedAmount;
    $this->approvalCode = $approvalCode;
    $this->traceNumber = $traceNumber;
    $this->referenceNumber = $referenceNumber;
  }

  function _get_authorizedAmount() {return $this->authorizedAmount;}
  function _get_approvalCode() {return $this->approvalCode;}
  function _get_traceNumber() {return $this->traceNumber;}
  function _get_referenceNumber() {return $this->referenceNumber;}
}
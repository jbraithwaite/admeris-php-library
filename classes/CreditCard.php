<?php namespace Admeris;

use Admeris\Base;

class CreditCard extends Base{

  private $number;
  private $expiryDate;
  private $cvv2;
  private $street;
  private $zip;
  private $secureCode;

  public function _get_number() {return $this->number;}
  public function _get_expiryDate() {return $this->expiryDate;}
  public function _get_cvv2() {return $this->cvv2;}
  public function _get_street() {return $this->street;}
  public function _get_zip() {return $this->zip;}
  public function _get_secureCode() {return $this->secureCode;}

  public function _set_number($data) {$this->number = $data;}
  public function _set_expiryDate($data) {$this->expiryDate = $data;}
  public function _set_cvv2($data) {$this->cvv2 = $data;}
  public function _set_street($data) {$this->street = $data;}
  public function _set_zip($data) {$this->zip = $data;}
  public function _set_secureCode($data) {$this->secureCode = $data;}
}
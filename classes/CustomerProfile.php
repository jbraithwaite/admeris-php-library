<?php namespace Admeris;

use Admeris\Base;

class CustomerProfile extends Base {

  private $address1;
  private $address2;
  private $city;
  private $country;
  private $faxNumber;
  private $firstName;
  private $lastName;
  private $legalName;
  private $phoneNumber;
  private $postal;
  private $province;
  private $tradeName;
  private $website;

  function _get_address1() {return $this->address1;}
  function _get_address2() {return $this->address2;}
  function _get_city() {return $this->city;}
  function _get_country() {return $this->country;}
  function _get_faxNumber() {return $this->faxNumber;}
  function _get_firstName() {return $this->firstName;}
  function _get_lastName() {return $this->lastName;}
  function _get_legalName() {return $this->legalName;}
  function _get_phoneNumber() {return $this->phoneNumber;}
  function _get_postal() {return $this->postal;}
  function _get_province() {return $this->province;}
  function _get_tradeName() {return $this->tradeName;}
  function _get_website() {return $this->website;}

  function _set_address1($address1) {$this->address1 = $address1;}
  function _set_address2($address2) {$this->address2 = $address2;}
  function _set_city($city) {$this->city = $city;}
  function _set_country($country) {$this->country = $country;}
  function _set_faxNumber($faxNumber) {$this->faxNumber = $faxNumber;}
  function _set_firstName($firstName) {$this->firstName = $firstName;}
  function _set_lastName($lastName) {$this->lastName = $lastName;}
  function _set_legalName($legalName) {$this->legalName = $legalName;}
  function _set_phoneNumber($phoneNumber) {$this->phoneNumber = $phoneNumber;}
  function _set_postal($postal) {$this->postal = $postal;}
  function _set_province($province) {$this->province = $province;}
  function _set_tradeName($tradeName) {$this->tradeName = $tradeName;}
  function _set_website($website) {$this->website = $website;}


  function isBlank() {
    return(
    !(($this->$firstName != null && strlen($this->$firstName) > 0)
    || ($this->$lastName != null && strlen($this->$lastName) > 0)
    || ($this->$legalName != null && strlen($this->$legalName) > 0)
    || ($this->$tradeName != null && strlen($this->$tradeName) > 0)
    || ($this->$address1 != null && strlen($this->$address1) > 0)
    || ($this->$address2 != null && strlen($this->$address2) > 0)
    || ($this->$city != null && strlen($this->$city) > 0)
    || ($this->$province != null && strlen($this->$province) > 0)
    || ($this->$postal != null && strlen($this->$postal) > 0)
    || ($this->$country != null && strlen($this->$country) > 0)
    || ($this->$website != null && strlen($this->$website) > 0)
    || ($this->$phoneNumber != null && strlen($this->$phoneNumber) > 0)
    || ($this->$faxNumber != null && strlen($this->$faxNumber) > 0)
    )
    );
  }
}
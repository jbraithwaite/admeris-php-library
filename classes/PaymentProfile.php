<?php namespace Admeris;

use Admeris\Base;

class PaymentProfile extends Base {

  private $creditCard;
  private $customerProfile;

  function _get_creditCard()
  {
    return $this->creditCard;
  }

  function _get_customerProfile()
  {
    return $this->customerProfile;
  }

  function _set_creditCard($newCreditCard)
  {
    $this->creditCard = $newCreditCard;
  }

  function _set_customerProfile($newCustomerProfile)
  {
    $this->customerProfile = $newCustomerProfile;
  }
}
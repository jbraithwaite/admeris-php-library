<?php namespace Admeris;

use Admeris\Base;

class Merchant extends Base{

  private $merchantId;
  private $apiToken;
  private $storeId;

  public function __construct($merchantId = ADMERIS_MERCHANT_ID, $apiToken = ADMERIS_API_TOKEN, $storeId = null)
  {
    $this->merchantId = $merchantId;
    $this->apiToken = $apiToken;
    $this->storeId = $storeId;
  }

  function _get_merchantId()
  {
    return $this->merchantId;
  }

  function _get_apiToken()
  {
    return $this->apiToken;
  }

  function _get_storeId()
  {
    return $this->storeId;
  }
}
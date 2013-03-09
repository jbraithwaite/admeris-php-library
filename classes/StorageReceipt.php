<?php namespace Admeris;

use Admeris\Base;

class StorageReceipt extends Base {

  protected $params = array();
  private $approved = FALSE;
  private $transactionId;
  private $orderId;
  private $processedDateTime;
  private $errorCode;
  private $errorMessage;
  private $debugMessage;
  private $response;
  private $paymentProfile;
  private $storageTokenId;

  public function __construct($response)
  {
    if ($response == NULL)
    {
      return;
    }

    $this->response = $response;

    $lines = explode("\n", $this->response);

    $size = count($lines);
    for ($i = 0; $i < $size-1; $i++)
    {
      list($paramKey, $paramValue) = explode("=", $lines[$i]);
      $this->params[$paramKey] = $paramValue;
    }

    $this->approved = $this->get_param('APPROVED') == 'true';
    $this->storageTokenId = $this->get_param('STORAGE_TOKEN_ID');
    $this->errorCode = $this->get_param('ERROR_CODE');
    $this->errorMessage = $this->get_param('ERROR_MESSAGE');
    $this->debugMessage = $this->get_param('DEBUG_MESSAGE');

    // make sure profile available
    $paymentProfileAvailable = $this->get_param('PAYMENT_PROFILE_AVAILABLE');
    // parse the profile
    if ($paymentProfileAvailable != NULL && $paymentProfileAvailable)
    {
      // parse the CreditCard
      $creditCard = NULL;
      $creditCardAvailable = $this->get_param('CREDIT_CARD_AVAILABLE');
      if ($creditCardAvailable != NULL && $creditCardAvailable) {
        $sanitized = $this->get_param('CREDIT_CARD_NUMBER');
        $sanitized = str_replace("\\*","",$sanitized);
        $creditCard = new CreditCard($sanitized, $this->get_param('EXPIRY_DATE'));
      }
      // parse the Customer Profile
      $profile = NULL;
      $customerProfileAvailable = $this->get_param('CUSTOMER_PROFILE_AVAILABLE');
      if ($customerProfileAvailable != NULL && $customerProfileAvailable) {
        $profile = new CustomerProfile();
        $profile->setLegalName($this->get_param('CUSTOMER_PROFILE_LEGAL_NAME'));
        $profile->setTradeName($this->get_param('CUSTOMER_PROFILE_TRADE_NAME'));
        $profile->setWebsite($this->get_param('CUSTOMER_PROFILE_WEBSITE'));
        $profile->setFirstName($this->get_param('CUSTOMER_PROFILE_FIRST_NAME'));
        $profile->setLastName($this->get_param('CUSTOMER_PROFILE_LAST_NAME'));
        $profile->setPhoneNumber($this->get_param('CUSTOMER_PROFILE_PHONE_NUMBER'));
        $profile->setFaxNumber($this->get_param('CUSTOMER_PROFILE_FAX_NUMBER'));
        $profile->setAddress1($this->get_param('CUSTOMER_PROFILE_ADDRESS1'));
        $profile->setAddress2($this->get_param('CUSTOMER_PROFILE_ADDRESS2'));
        $profile->setCity($this->get_param('CUSTOMER_PROFILE_CITY'));
        $profile->setProvince($this->get_param('CUSTOMER_PROFILE_PROVINCE'));
        $profile->setPostal($this->get_param('CUSTOMER_PROFILE_POSTAL'));
        $profile->setCountry($this->get_param('CUSTOMER_PROFILE_COUNTRY'));
      }
      $this->paymentProfile = new PaymentProfile();
      $this->paymentProfile->creditCard = $creditCard;
      $this->paymentProfile->profile = $profile;
    }
    else
    {
      $this->paymentProfile = NULL;
    }

  }

  public static function errorOnlyReceipt($errorCode, $errorMessage = NULL, $debugMessage = NULL)
  {
    $theReceipt = new StorageReceipt();
    $theReceipt->errorCode = $errorCode;
    $theReceipt->errorMessage = $errorMessage;
    $theReceipt->debugMessage = $debugMessage;
    $theReceipt->processedDateTimestamp = time();
    $theReceipt->processedDateTime = date('r', $theReceipt->processedDateTimestamp);
    return $theReceipt;
  }

  function _get_paymentProfile() {return $this->paymentProfile;}
  function _get_storageTokenId() {return $this->storageTokenId;}
  function _get_debugMessage() {return $this->debugMessage;}
  function _get_errorCode() {return $this->errorCode;}
  function _get_errorMessage() {return $this->errorMessage;}
  function _get_orderId() {return $this->orderId;}
  function _get_processedDateTime() {return $this->processedDateTime;}
  function _get_transactionId() {return $this->transactionId;}
  function _get_approved() {return $this->approved;}
}
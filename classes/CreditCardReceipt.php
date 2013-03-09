<?php namespace Admeris;

use Admeris\ApprovalInfo;
use Admeris\AvsResponse;
use Admeris\Base;
use Admeris\CreditCardReceipt;
use Admeris\Cvv2Response;
use Admeris\PeriodicPurchaseInfo;

class CreditCardReceipt extends Base{

  protected $params = array();
  private $approved = false;
  private $transactionId;
  private $orderId;
  private $processedDateTime;  // as a string
  private $processedDateTimestamp; // as an int (can apply your own formatting to this value)
  private $errorCode;
  private $errorMessage;
  private $debugMessage;
  private $approvalInfo;
  private $avsResponse;
  private $cvv2Response;
  private $response;
  private $periodicPurchaseInfo;

  // constructor parses response from gateway into this object
  public function __construct($response = NULL) {

    if ($response == NULL) {
      return;
    }

    // parse response into param associative array

    $this->response = $response;
    $lines = explode("\n", $this->response);

    $size = count($lines);
    for ($i = 0; $i < $size-1; $i++)
    {
      list($paramKey, $paramValue) = explode("=", $lines[$i]);
      $this->params[$paramKey] = $paramValue;
    }

    // parse the param into data class objects
    $this->approved = $this->get_param('APPROVED') == 'true';

    $this->transactionId = $this->get_param('TRANSACTION_ID');
    $this->orderId = $this->get_param('ORDER_ID');
    // returned date time is in yymmddhhiiss format
    $processedDate = $this->get_param('PROCESSED_DATE');
    $processedTime = $this->get_param('PROCESSED_TIME');

    if ($processedDate != NULL && $processedTime != NULL) {
      $year = substr($processedDate, 0, 2);
      $month = substr($processedDate, 2, 2);
      $day = substr($processedDate, 4, 2);
      $hour = substr($processedTime, 0, 2);
      $minute = substr($processedTime, 2, 2);
      $second = substr($processedTime, 4, 2);
      $this->processedDateTimestamp = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second);
      $this->processedDateTime = date('r', $this->processedDateTimestamp);
    } else {
      $this->processedDateTime = NULL;
    }
    $this->errorCode = $this->get_param('ERROR_CODE');
    $this->errorMessage = $this->get_param('ERROR_MESSAGE');
    $this->debugMessage = $this->get_param('DEBUG_MESSAGE');

    // parse the approval info
    if ($this->approved) {
      $this->approvalInfo = new ApprovalInfo( $this->get_param('AUTHORIZED_AMOUNT'), $this->get_param('APPROVAL_CODE'), $this->get_param('TRACE_NUMBER'), $this->get_param('REFERENCE_NUMBER'));
    }
    else
    {
      $this->approvalInfo = NULL;
    }

    // parse the AVS response
    $avsResponseAvailable = $this->get_param('AVS_RESPONSE_AVAILABLE');

    if ($avsResponseAvailable != NULL && $avsResponseAvailable) {

      $avsErrorCode = $this->get_param('AVS_ERROR_CODE');
      $avsErrorMessage = $this->get_param('AVS_ERROR_MESSAGE');

      $this->avsResponse = new AvsResponse();
      $this->avsResponse->avsResponseCode = $this->get_param('AVS_RESPONSE_CODE');
      $this->avsResponse->streetMatched =  $this->get_param('STREET_MATCHED');
      $this->avsResponse->zipMatched = $this->get_param('ZIP_MATCHED');
      $this->avsResponse->zipType = $this->get_param('ZIP_TYPE');
      $this->avsResponse->avsErrorCode = $avsErrorCode;
      $this->avsResponse->avsErrorMessage = $avsErrorMessage;
    }
    else
    {
      $this->avsResponse = NULL;
    }

    // parse the CVV2 response
    $cvv2ResponseAvailable = $this->get_param('CVV2_RESPONSE_AVAILABLE');

    if ($cvv2ResponseAvailable != NULL && $cvv2ResponseAvailable)
    {
      $this->cvv2Response = new Cvv2Response( $this->get_param('CVV2_RESPONSE_CODE'), $this->get_param('CVV2_RESPONSE_MESSAGE'));
    }
    else
    {
      $this->cvv2Response = NULL;
    }

    // parse periodic purchase info
    $periodicPurchaseId = $this->get_param('PERIODIC_TRANSACTION_ID');

    if ($periodicPurchaseId != NULL)
    {
      $periodicPurchaseState = $this->get_param('PERIODIC_TRANSACTION_STATE');
      $periodicNextPaymentDate = $this->get_param('PERIODIC_NEXT_PAYMENT_DATE');
      $periodicLastPaymentId = $this->get_param('PERIODIC_LAST_PAYMENT_ID');

      $this->periodicPurchaseInfo = new PeriodicPurchaseInfo();
      $this->periodicPurchaseInfo->periodicTransactionId = $periodicPurchaseId;
      $this->periodicPurchaseInfo->state = $periodicPurchaseState;
      $this->periodicPurchaseInfo->nextPaymentDate = $periodicNextPaymentDate;
      $this->periodicPurchaseInfo->lastPaymentId = $periodicLastPaymentId;
    }
    else
    {
      $this->periodicPurchaseInfo = NULL;
    }
  }

  // returns an error-only receipt (used when unable to connect to
  // gateway or process request).
  public static function errorOnlyReceipt($errorCode, $errorMessage = NULL, $debugMessage = NULL)
  {
    $theReceipt = new CreditCardReceipt();
    $theReceipt->errorCode = $errorCode;
    $theReceipt->errorMessage = $errorMessage;
    $theReceipt->debugMessage = $debugMessage;
    $theReceipt->processedDateTimestamp = time();
    $theReceipt->processedDateTime = date('r', $theReceipt->processedDateTimestamp);

    return $theReceipt;
  }

  function _get_approvalInfo() {return $this->approvalInfo;}
  function _get_avsResponse() {return $this->avsResponse;}
  function _get_cvv2Response() {return $this->cvv2Response;}
  function _get_debugMessage() {return $this->debugMessage;}
  function _get_errorCode() {return $this->errorCode;}
  function _get_errorMessage() {return $this->errorMessage;}
  function _get_orderId() {return $this->orderId;}
  function _get_processedDateTime() {return $this->processedDateTime;}
  function _get_transactionId() {return $this->transactionId;}
  function _get_periodicPurchaseInfo() {return $this->periodicPurchaseInfo;}
  function _get_approved() {return $this->approved;}
}
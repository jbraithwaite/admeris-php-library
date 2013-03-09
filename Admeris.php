<?php

// Admeris Credit Card Core API for PHP

// error codes
define('REQ_MALFORMED_URL',-1);
define('REQ_POST_ERROR',-2);
define('REQ_RESPONSE_ERROR',-4);
define('REQ_CONNECTION_FAILED',-5);
define('REQ_INVALID_REQUEST',-6);

// market segment
define('MARKET_SEGMENT_INTERNET', 'I');
define('MARKET_SEGMENT_MOTO', 'M');
define('MARKET_SEGMENT_RETAIL', 'G');

// AVS request
define('AVS_VERIFY_STREET_AND_ZIP', 0);
define('AVS_VERIFY_ZIP_ONLY', 1);

// Cvv2 request
define('CVV2_NOT_SUBMITTED', 0);
define('CVV2_PRESENT', 1);
define('CVV2_PRESENT_BUT_ILLEGIBLE', 2);
define('CVV2_HAS_NO_CVV2', 9);

define('NEW',0);
define('IN_PROGRESS',1);
define('COMPLETE',2);
define('ON_HOLD', 3);
define('CANCELLED',4);

define('DATE_FORMAT', 'yymmdd');

use Admeris\Merchant;
use Admeris\StorageReceipt;
use Admeris\CreditCardReceipt;

class Admeris {

	private $merchant;
	private $marketSegment;
	private $url;

	function __construct ($merchantId, $apiToken, $url, $marketSegment = MARKET_SEGMENT_INTERNET){
		$this->merchant = new Merchant($merchantId, $apiToken);
		$this->marketSegment = $marketSegment;
		$this->url = $url;
	}

	/**
	 * Refund
	 *
	 * @param  int $purchaseId
	 * @param  int $purchaseOrderId
	 * @param  int $refundOrderId
	 * @param  int $amount
	 */
	function refund($purchaseId, $purchaseOrderId, $refundOrderId, $amount) {

		if ($purchaseOrderId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "purchaseOrderId is required");
		}

		$req = array();

		$this->appendHeader($req, "refund");
		$this->appendTransactionId($req, $purchaseId);
		$this->appendTransactionOrderId($req, $purchaseOrderId);
		$this->appendOrderId($req, $refundOrderId);
		$this->appendAmount($req, $amount);

		return $this->send($req, "creditcard");
	}

	/**
	 * Single Purchase
	 *
	 * @param  int $orderId
	 * @param  object $creditCardSpecifier
	 * @param  numeric $amount
	 * @param  object $verificationRequest
	 */
	function singlePurchase ($orderId, $creditCardSpecifier, $amount, $verificationRequest)
	{
		if ($creditCardSpecifier == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "creditcard or storageTokenId is required");
		}

		if ($orderId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "orderId is required");
		}

		// create the request
		$req = array();

		$this->appendHeader($req, "singlePurchase");
		$this->appendOrderId($req, $orderId);

		if (is_string($creditCardSpecifier))
		{
			$this->appendStorageTokenId($req, $creditCardSpecifier);
		}
		else
		{
			$this->appendCreditCard($req, $creditCardSpecifier);
		}

		$this->appendAmount($req, $amount);
		$this->appendVerificationRequest($req, $verificationRequest);

		return $this->send($req, "creditcard");
	}

	//CHECK
	function installmentPurchase ($orderId, $creditCard, $preinstallmentamount,	$startDate, $totalNumberInstallments ,$verificationRequest)
	{
		if ($order == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "orderId is required");
		}

		if ($creditCard == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "creditCard is required");
		}

		$req = array();

		$this->appendHeader($req, "installmentPurchase");
		$this->appendOrderId($req, $orderId);
		$this->appendCreditCard($req, $creditCard);
		$this->appendAmount($req, $preinstallmentamount);
		$this->appendStartDate($req, $startDate);
		$this->appendTotalNumberInstallments($req, $totalNumberInstallments);
		$this->appendVerificationRequest($req, $verificationRequest);

		return $this->send($req, "creditcard");
	}

	function recurringPurchase($periodicPurchaseInfo, $creditCardSpecifier, $verificationRequest)
	{
		if ($creditCardSpecifier == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "creditcard or storageTokenId is required");
		}

		if ($periodicPurchaseInfo->orderId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "orderId is required");
		}

		$req = array();
		$this->appendHeader($req, "recurringPurchase");
		$this->appendOperationType($req, "create");
		$this->appendOrderId($req, $periodicPurchaseInfo->orderId);
		$this->appendAmount($req, $periodicPurchaseInfo->perPaymentAmount);
		$this->appendStartDate($req, $periodicPurchaseInfo->startDate);
		$this->appendEndDate($req, $periodicPurchaseInfo->endDate);
		$this->appendPeriodicPurchaseSchedule($req, $periodicPurchaseInfo->schedule);
		$this->appendVerificationRequest($req, $verificationRequest);

		if (is_string($creditCardSpecifier))
		{
			$this->appendStorageTokenId($req, $creditCardSpecifier);
			return $this -> send($req, "storage");
		}
		else
		{
			$this->appendCreditCard($req, $creditCardSpecifier);
			return $this -> send($req, "creditcard");
		}
	}

	//CHECK
	function holdRecurringPurchase($recurringPurchaseId)
	{
		$PPI = new PeriodicPurchaseInfo();

		$PPI->periodicTransactionId = $recurringPurchaseId;
		$PPI->state = ON_HOLD;

		return $this->updateRecurringPurchaseHelper($PPI);
	}

	function resumeRecurringPurchase($recurringPurchaseId)
	{
		$PPI = new PeriodicPurchaseInfo();

		$PPI->periodicTransactionId = $recurringPurchaseId;
		$PPI->state = IN_PROGRESS;

		return $this->updateRecurringPurchaseHelper($PPI);
	}

	function cancelRecurringPurchase($recurringPurchaseId)
	{
		$PPI = new PeriodicPurchaseInfo();

		$PPI->periodicTransactionId = $recurringPurchaseId;
		$PPI->state = CANCELLED;

		return $this->updateRecurringPurchaseHelper($PPI);
	}

	function queryRecurringPurchase($recurringPurchaseId)
	{
		if ($recurringPurchaseId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "recurringPurchaseId is required");
		}

		$req = array();
		$this->appendHeader($req, "recurringPurchase");
		$this->appendOperationType($req, "query");
		$this->appendTransactionId($req, $recurringPurchaseId);

    return $this->send($req, "creditcard");
	}

	function updateRecurringPurchase( $recurringPurchaseId, $creditCardSpecifier,	$perPaymentAmount, $verificationRequest, $state)
	{
		if ($creditCardSpecifier == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "creditcard or storageTokenId is required");
		}

		if ($recurringPurchaseId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "recurringPurchaseId is required");
		}

		$periodicPurchaseInfo = new PeriodicPurchaseInfo ();
		$periodicPurchaseInfo->recurringPurchaseId = $recurringPurchaseId;
		$periodicPurchaseInfo->state = $state;
		$periodicPurchaseInfo->perPaymentAmount = $perPaymentAmount;

		return updateRecurringPurchaseHelper($periodicPurchaseInfo, $creditCardSpecifier, $verificationRequest);
	}

	function updateRecurringPurchase2( $periodicPurchaseInfo, $creditCardSpecifier, $verificationRequest)
	{
		if ($creditCardSpecifier == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "creditcard or storageTokenId is required");
		}

		if ($periodicPurchaseInfo->periodicTransactionId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "recurringPurchaseId is required");
		}

		return $this->updateRecurringPurchaseHelper($periodicPurchaseInfo, $creditCardSpecifier, $verificationRequest);
	}

	function updateRecurringPurchaseHelper($periodicPurchaseInfo, $creditCardSpecifier = NULL, $verificationRequest = NULL)
	{
		$req = array();
		$this->appendHeader($req, "recurringPurchase");
		$this->appendOperationType($req, "update");
		$this->appendTransactionId($req, $periodicPurchaseInfo->periodicTransactionId());

		if (is_string($creditCardSpecifier))
		{
			$this->appendStorageTokenId($req, $creditCardSpecifier);
		}
		else
		{
			$this->appendCreditCard($req, $creditCardSpecifier);
		}

		if ($verificationRequest != NULL)
		{
			$this->appendVerificationRequest($req, $verificationRequest);
		}

		$this->appendPeriodicPurchaseInfo($req, $periodicPurchaseInfo);

		return $this->send($req, "creditcard");
	}

	/**
	 * Verify Credit Card
	 *
	 */
	function verifyCreditCard($creditCardSpecifier, $verificationRequest)
	{
		if ($creditCardSpecifier == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "storageTokenId is required");
		}

		if ($verificationRequest == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "verificationRequest is required");
		}

		$req = array();

		$this->appendHeader($req, "verifyCreditCard");

		if (is_string($creditCardSpecifier))
		{
			$this->appendStorageTokenId($req, $creditCardSpecifier);
		}
		else
		{
			$this->appendCreditCard($req, $creditCardSpecifier);
		}

		$this->appendVerificationRequest($req, $verificationRequest);

		return $this->send($req, "creditcard");
	}

	/**
	 * Void Transaction
	 *
	 * @param  int $transactionId
	 * @param  int $transactionOrderId
	 *
	 * @return [type] [description]
	 */
	function voidTransaction($transactionId, $transactionOrderId)
	{
		if ($transactionOrderId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "transactionOrderId is required");
		}

		$req = array();

		$this->appendHeader($req, "void");
		$this->appendTransactionId($req, $transactionId);
		$this->appendTransactionOrderId($req, $transactionOrderId);

		return $this->send($req, "creditcard");
	}

	/**
	 * Verify Transaction
	 *
	 * @param  int $transactionId
	 * @param  int $transactionOrderId
	 *
	 * @return [type] [description]
	 */
	function verifyTransaction($transactionId, $transactionOrderId = NULL)
	{
		if ($transactionOrderId == NULL && $transactionId == NULL)
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "at least one of transactionId or transactionOrderId is required");
		}

		$req = array();

		$this->appendHeader($req, "verifyTransaction");
		$this->appendTransactionId($req, $transactionId);
		$this->appendTransactionOrderId($req, $transactionOrderId);

		return $this->send($req, "creditcard");
	}

	/**
	 * Add To Storage
	 *
	 * @param int $storageTokenId
	 * @param object $paymentProfile
	 */
	function addToStorage ($storageTokenId, $paymentProfile)
	{
		if ($paymentProfile == NULL)
		{
			return StorageReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "payment profile is required");
		}

		$req = array();

		$this->appendHeader($req, "secureStorage");
		$this->appendOperationType($req, "create");
		$this->appendStorageTokenId($req, $storageTokenId);
		$this->appendPaymentProfile($req, $paymentProfile);

		return $this->send($req, "storage");
	}

	/**
	 * Delete from storage
	 *
	 * @param  int $storageTokenId
	 */
	function deleteFromStorage ($storageTokenId)
	{
		if ($storageTokenId == NULL)
		{
			return StorageReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "storageTokenId is required");
		}

		$req = array();

		$this->appendHeader($req, "secureStorage");
		$this->appendOperationType($req, "delete");
		$this->appendStorageTokenId($req, $storageTokenId);

		return $this->send($req, "storage");
	}

	/**
	 * Query Storage
	 *
	 * @param  int $storageTokenId
	 */
	function queryStorage ($storageTokenId)
	{
		if ($storageTokenId == NULL)
		{
			return StorageReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "storageTokenId is required");
		}

		$req = array();

		$this->appendHeader($req, "secureStorage");
		$this->appendOperationType($req, "query");
		$this->appendStorageTokenId($req, $storageTokenId);

		return $this->send($req, "storage");
	}

	/**
	 * Update Storage
	 *
	 * @param  int $storageTokenId
	 * @param  object $paymentProfile
	 */
	function updateStorage($storageTokenId, $paymentProfile)
	{
		if ($storageTokenId == NULL)
		{
			return StorageReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "storageTokenId is required");
		}

		if ($paymentProfile == NULL)
		{
			return StorageReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST, "payment profile is required");
		}

		$req = array();

		$this->appendHeader($req, "secureStorage");
		$this->appendOperationType($req, "update");
		$this->appendStorageTokenId($req, $storageTokenId);
		$this->appendPaymentProfile($req, $paymentProfile);

		return $this->send($req, "storage");
	}

	/**
	 * Appent Amount
	 *
	 * @param  array $req
	 * @param  numeric $amount
	 */
	function appendAmount(&$req, $amount)
	{
		$this->appendParam($req, "amount", $amount);
	}

	/**
	 * Append API Token
	 *
	 * @param  array $req
	 * @param  string $apiToken
	 */
	function appendApiToken(&$req, $apiToken)
	{
		$this->appendParam($req, "apiToken", $apiToken);
	}

	/**
	 * Append Credit Card
	 *
	 * @param  array $req
	 * @param  string $creditCard
	 */
	function appendCreditCard(&$req, $creditCard) {
		if ($creditCard != NULL)
		{
			$this->appendParam($req, "creditCardNumber", $creditCard->number);
			$this->appendParam($req, "expiryDate", $creditCard->expiryDate);
			$this->appendParam($req, "cvv2", $creditCard->cvv2);
			$this->appendParam($req, "street", $creditCard->street);
			$this->appendParam($req, "zip", $creditCard->zip);
			$this->appendParam($req, "secureCode", $creditCard->secureCode);
		}
	}

	/**
	 * Append Header
	 *
	 * @param  array $req
	 * @param  string $requestCode
	 */
	function appendHeader(&$req, $requestCode)
	{
		$this->appendParam($req, "requestCode", $requestCode);
		$this->appendMerchantId($req, $this->merchant->merchantId);
		$this->appendApiToken($req, $this->merchant->apiToken);
		$this->appendParam($req, "marketSegmentCode", $this->marketSegment);
	}

	/**
	 * Append Operation Type
	 *
	 * @param  array $req
	 * @param  string $type
	 */
	function appendOperationType(&$req, $type)
	{
		if ($type != NULL)
		{
			$this->appendParam($req, "operationCode", $type);
		}
	}

	/**
	 * Append Periodic Purchase State
	 *
	 * @param  array $req
	 * @param  string $state
	 */
	function appendPeriodicPurchaseState(&$req, $state)
	{
		if ($state != NULL)
		{
			$this->appendParam($req, "periodicPurchaseStateCode", $state);
		}
	}

	/**
	 * Append Periodic Purchase Schedule
	 *
	 * @param  array $req
	 * @param  string $schedule
	 */
	function appendPeriodicPurchaseSchedule(&$req, $schedule)
	{
		if ($schedule != NULL)
		{
			$this->appendParam($req, "periodicPurchaseScheduleTypeCode", $schedule->scheduleType);
			$this->appendParam($req, "periodicPurchaseIntervalLength", $schedule->intervalLength);
		}
	}

	/**
	 * Append Periodic Purchase Info
	 *
	 * @param  array $req
	 * @param  string $periodicPurchaseInfo
	 */
	function appendPeriodicPurchaseInfo (&$req, $periodicPurchaseInfo)
	{
		if ($periodicPurchaseInfo->perPaymentAmount != NULL)
		{
			$this -> appendAmount ($req, $periodicPurchaseInfo->perPaymentAmount);
		}

		if ($periodicPurchaseInfo->state != NULL)
		{
			$this -> appendPeriodicPurchaseState($req, $periodicPurchaseInfo->state);
		}

		if ($periodicPurchaseInfo->schedule != NULL)
		{
			$this -> appendPeriodicPurchaseSchedule($req, $periodicPurchaseInfo->schedule);
		}

		if ($periodicPurchaseInfo->orderId != NULL)
		{
			$this -> appendOrderId($req, $periodicPurchaseInfo->orderId);
		}

		if ($periodicPurchaseInfo->customerId != NULL)
		{
			$this -> appendParam($req, "customerId", $periodicPurchaseInfo->customerId);
		}

		if ($periodicPurchaseInfo->startDate != NULL)
		{
			$this -> appendStartDate($req, $periodicPurchaseInfo->startDate);
		}

		if ($periodicPurchaseInfo->endDate != NULL)
		{
			$this -> appendEndDate($req, $periodicPurchaseInfo->endDate);
		}

		if ($periodicPurchaseInfo->nextPaymentDate != NULL)
		{
			$this -> appendDate($req, "nextPaymentDate", $periodicPurchaseInfo->nextPaymentDate);
		}

	}

	/**
	 * Append Merchant ID
	 *
	 * @param  array $req
	 * @param  numeric $merchantId
	 */
	function appendMerchantId(&$req, $merchantId)
	{
		$this->appendParam($req, "merchantId", $merchantId);
	}

	/**
	 * Append Order ID
	 *
	 * @param  [type] $req [description]
	 * @param  [type] $orderId [description]
	 */
	function appendOrderId(&$req, $orderId)
	{
		$this->appendParam($req, "orderId", $orderId);
	}

	/**
	 * Append Param
	 *
	 * @param  array $req
	 * @param  string $name
	 * @param  mixed $value
	 */
	function appendParam(&$req, $name, $value)
	{
   if (!is_NULL($value))
   {
			$req[$name] = $value;
   }
	}

	/**
	 * Append Transaction Id
	 *
	 * @param  array $req
	 * @param  numeric $transactionId
	 */
	function appendTransactionId(&$req, $transactionId)
	{
		$this->appendParam($req, "transactionId", $transactionId);
	}

	/**
	 * Append Transaction Order ID
	 *
	 * @param  array $req
	 * @param  numeric $transactionOrderId
	 */
	function appendTransactionOrderId(&$req,$transactionOrderId)
	{
		$this->appendParam($req, "transactionOrderId", $transactionOrderId);
	}

	/**
	 * Append Verification Request
	 *
	 * @param  array $req
	 * @param  object $vr
	 */
	function appendVerificationRequest(&$req,$vr)
	{
		if ($vr != NULL) {
			$this->appendParam($req, "avsRequestCode", $vr->avsRequest);
			$this->appendParam($req, "cvv2RequestCode", $vr->cvv2Request);
		}
	}

	/**
	 * Append Storage Token ID
	 *
	 * @param  array $req
	 * @param  numeric $storageTokenId
	 */
	function appendStorageTokenId (&$req, $storageTokenId)
	{
		$this->appendParam($req, "storageTokenId", $storageTokenId);
	}

	/**
	 * Append Total Number Installments
	 *
	 * @param  array $req
	 * @param  numeric $totalNumberInstallments
	 */
	function appendTotalNumberInstallments(&$req,	$totalNumberInstallments)
	{
		$this->appendParam($req, "totalNumberInstallments", $totalNumberInstallments);
	}

	/**
	 * Append Start Date
	 *
	 * @param  array $req
	 * @param  string $startDate
	 */
	function appendStartDate(&$req, $startDate)
	{
		if ($startDate != NULL)
		{
			$this->appendParam($req, "startDate", $startDate);
		}
	}

	/**
	 * Append End Date
	 *
	 * @param  array $req
	 * @param  string $endDate
	 */
	function appendEndDate(&$req, $endDate)
	{
		if ($endDate != NULL)
		{
			$this->appendParam($req, "endDate", $endDate);
		}
	}

	/**
	 * Append Payment Profile
	 *
	 * @param  array $req
	 * @param  [type] $paymentProfile [description]
	 */
	function appendPaymentProfile(&$req, $paymentProfile)
	{
		if ($paymentProfile == NULL)
		{
			return;
		}

		if ($paymentProfile->creditCard != NULL)
		{
			$this->appendCreditCard($req, $paymentProfile->creditCard);
		}

		if ($paymentProfile->customerProfile != NULL) {
			$customerProfile = $paymentProfile->customerProfile;
			$this->appendParam($req, "profileLegalName", $customerProfile->legalName);
			$this->appendParam($req, "profileTradeName", $customerProfile->yradeName);
			$this->appendParam($req, "profileWebsite", $customerProfile->website);
			$this->appendParam($req, "profileFirstName", $customerProfile->firstName);
			$this->appendParam($req, "profileLastName", $customerProfile->lastName);
			$this->appendParam($req, "profilePhoneNumber", $customerProfile->phoneNumber);
			$this->appendParam($req, "profileFaxNumber", $customerProfile->faxNumber);
			$this->appendParam($req, "profileAddress1", $customerProfile->address1);
			$this->appendParam($req, "profileAddress2", $customerProfile->address2);
			$this->appendParam($req, "profileCity", $customerProfile->city);
			$this->appendParam($req, "profileProvince", $customerProfile->province);
			$this->appendParam($req, "profilePostal", $customerProfile->postal);
			$this->appendParam($req, "profileCountry", $customerProfile->country);
		}
	}

	/**
	 * Sends a gateway request
	 *
	 */
	function send($request, $receipttype)
	{
		if ($request == NULL && $receipttype == "creditcard")
		{
			return CreditCardReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST,	'a request string is required 25');
		}

		if ($request == NULL && $receipttype == "storage")
		{
			return StorageReceipt::errorOnlyReceipt(REQ_INVALID_REQUEST,	'a request string is required');
		}

		$queryPairs = array();

		foreach($request as $key => $item)
		{
			$queryPairs[] .= urlencode($key) .'='. urlencode($item);
		}

		$query = implode('&', $queryPairs);

		$receipt = NULL;
		$response = NULL;

		// open http conn to gateway, post request
		$fp = NULL;

		//$fp = @fopen($this->url . '?' . $query, 'rb', false);

		if (phpversion() < 5)
		{
			$fp = @fopen($this->url . '?' . $query, 'rb', false);
		}
		else
		{
			$params = array('http' => array(
              			'method' => 'POST',
              			'content' => $query
            		));
			$ctx = stream_context_create($params);
  			$fp = @fopen($this->url, 'rb', false, $ctx);
		}

		if (!$fp && $receipttype == "creditcard")
		{
			$receipt = CreditCardReceipt::errorOnlyReceipt(REQ_POST_ERROR, 'error attempting to send POST request');
		}

		if (!$fp && $receipttype == "storage")
		{
			$receipt = StorageReceipt::errorOnlyReceipt(REQ_POST_ERROR, 'error attempting to send POST request');
		}

		$curline = @fgets($fp);

		if ($curline == false && $receipttype == "creditcard")
		{
			$receipt = CreditCardReceipt::errorOnlyReceipt(REQ_RESPONSE_ERROR, 'Could not obtain response from the credit card gateway.');
		}

		if ($curline == false && $receipttype == "storage")
		{
			$receipt = StorageReceipt::errorOnlyReceipt(REQ_RESPONSE_ERROR, 'Could not obtain response from the credit card gateway.');
		}
		else
		{
			while ($curline != false)
			{
				$response .= $curline;
				$curline = @fgets($fp);
			}
		}

		@fclose($fp);

		$fp = NULL;

		// parse receipt object from response content based on receipt type
		if ($receipttype == "creditcard")
		{
			$receipt = new CreditCardReceipt($response);
		}

		if ($receipttype == "storage")
		{
			$receipt = new StorageReceipt($response);
		}

		if ($fp != NULL)
		{
			@fclose($fp);
		}

		return $receipt;
	}
}
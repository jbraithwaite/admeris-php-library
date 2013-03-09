<?php
include '../HttpsCreditCardService.php';

/** An example of how to create a Recurring Purchase using the Admeris Credit Card Core API */

// connection parameters to the Admeris CC gateway
$url = 'https://test.admeris.com/ccgateway/cc/processor.do';
$merchant = new Merchant ('yourMerchatId', 'yourApiToken');
$service = new HttpsCreditCardService($merchant, $url);

// credit card info from customer
$creditCard = new CreditCard('4242424242424242', '1010', '111', '123 Street', 'A1B23C');

// recur schedule (in this example, every 2 weeks)
$schedule = new Schedule(WEEK, 2);

// set a recurring purchase to run from 2009-10-10 until 2009-12-10
// Note that date format is 'yymmdd'
$periodicPurchaseInfo = new PeriodicPurchaseInfo (null, null, $schedule, '1000','recurring-001', null, '091010', '091210', null, null);
$receipt = $service->recurringPurchase2($periodicPurchaseInfo, $creditCard, null);

// Show result (see DataClasses.php, class CreditCardReceipt for more fields)
echo 'Approved: ' . $receipt->isApproved();
echo '<br/>';
echo 'Periodic Txn ID: ' . $receipt->getPeriodicPurchaseInfo()->getPeriodicTransactionId();
echo '<br/>';
echo 'Debug Message: ' . $receipt->getDebugMessage();
?>

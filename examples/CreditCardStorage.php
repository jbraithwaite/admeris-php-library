<?php
include '../HttpsCreditCardService.php';

/** An example of using the Admeris Secure Profile Storage API to store then use the stored Credit Card */

// connection parameters to the Admeris CC gateway
$url = 'https://test.admeris.com/ccgateway/cc/processor.do';
$merchant = new Merchant ('yourMerchatId', 'yourApiToken');
$service = new HttpsCreditCardService($merchant, $url);

// credit card info from customer - to be stored
$creditCard = new CreditCard('5555555555554444', '1010', null, '123 Street', 'A1B23C');

// payment profile to be stored (just using the card component in this example)
$paymentProfile = new PaymentProfile($creditCard, null);

// store data under the token 'my-token-001'
$storageToken = 'my-token-001';
$receipt = $service->addToStorage($storageToken, $paymentProfile);

// Approved?
echo 'Storage Approved: ' . $receipt->isApproved();

// if stored, now use in a purchase
if ($receipt->isApproved() != 'false') {
	// send request
	$receipt = $service->singlePurchase('stored-card-001', $storageToken, '100', null);
	// array dump of response params
	echo '<br/>';
	echo 'Single Purchase with stored card results: '; 
	print_r($receipt->params);
}

?>

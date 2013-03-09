Refactored Admeris PHP Core API
===============================

## Background


[Admeris Payment Systems](http://www.admeris.com/) is a full-service provider of credit, debit, loyalty and gift card payment processing solutions, in partnership with the world’s largest financial institutions and technology partners.

The only problem is that thier PHP library is poorly written.

## Goals of refactoring

- Namespacing
- Bug fixes
- One class per file
- Modern OO code (PHP 5+)
- Remain relatively compatible with the [documentation](http://www.admeris.com/developers/downloads/).

## Updated examples

### Store a CreditCard

    $service = new Admeris(ADMERIS_MERCHANT_ID, ADMERIS_API_TOKEN, ADMERIS_URL);

    // No more crazy constructor
    $creditCard = new CreditCard();

    $creditCard->number = '5555555555554444';
    $creditCard->expiryDate = '1010';
    $creditCard->street = '123 Street';
    $creditCard->zip = 'A1B23C';

    $paymentProfile = new PaymentProfile();
    $paymentProfile->creditCard = $creditCard;

    $storageToken = 'my-token-001';
    $receipt = $service->addToStorage($storageToken, $paymentProfile);

    if ($receipt->approved)
    {
      echo 'Successfully Stored';
    }

### Create a subscription

    $service = new Admeris(ADMERIS_MERCHANT_ID, ADMERIS_API_TOKEN, ADMERIS_URL);

    // No more crazy constructor
    $creditCard = new CreditCard();

    $creditCard->number = '5555555555554444';
    $creditCard->expiryDate = '1010';
    $creditCard->street = '123 Street';
    $creditCard->zip = 'A1B23C';

    $paymentProfile = new PaymentProfile();
    $paymentProfile->creditCard = $creditCard;

    // No more crazy constants
    $schedule = new Schedule('week', 2);

    // No more crazy constructor
    $periodicPurchaseInfo = new PeriodicPurchaseInfo();
    $periodicPurchaseInfo->orderId = '10012';
    $periodicPurchaseInfo->perPaymentAmount = '1000';
    $periodicPurchaseInfo->startDate = '130101';
    $periodicPurchaseInfo->endDate = '140101';
    $periodicPurchaseInfo->schedule = $schedule;

    $receipt = $service->recurringPurchase($periodicPurchaseInfo, $creditCard, null);

    if ($receipt->approved)
    {
        echo 'Subscription approved';
    }

--------

Copyright (c) 2013 Justin Braithwaite

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
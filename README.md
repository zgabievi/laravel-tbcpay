# laravel-tbcpay

> Some great updates are comming soon...

[![Latest Stable Version](https://poser.pugx.org/zgabievi/TBC/version?format=flat-square)](https://packagist.org/packages/zgabievi/tbc)
[![Total Downloads](https://poser.pugx.org/zgabievi/TBC/d/total?format=flat-square)](https://packagist.org/packages/zgabievi/tbc)
[![License](https://poser.pugx.org/zgabievi/TBC/license?format=flat-square)](https://packagist.org/packages/zgabievi/tbc)
[![StyleCI](https://styleci.io/repos/49653979/shield)](https://styleci.io/repos/49653979)

| TBC Payment |     |
|:-----------:|:----|
| [![TBC Payment](https://i.imgsafe.org/fbbe3ce20f.png)](https://github.com/zgabievi/laravel-tbcpay) | "TBC" payment integration for [Laravel 5.*](http://laravel.com/), created for Georgian developers. :bulb: Inspired by [tbcpay-php](https://github.com/wearede/tbcpay-php) from [Sandro Dzneladze](https://github.com/sandrodz) :tada: Pull requests are welcome. |

## Table of Contents
- [Installation](#installation)
    - [Composer](#composer)
    - [Laravel](#laravel)
- [Documentation](#documentation)
- [Usage](#usage)
- [Examples](#examples)
- [Codes](#codes)
- [Config](#config)
- [License](#license)

## Installation

### Composer

Run composer command in your terminal.

    composer require zgabievi/tbc

### Laravel

Open `config/app.php` and find the `providers` key. Add `TBCServiceProvider` to the array.

```php
Gabievi\TBC\TBCServiceProvider::class
```

Find the `aliases` key and add `Facade` to the array. 

```php
'TBC' => Gabievi\TBC\TBCFacade::class
```

## Documentation

There are two types of transaction within this system:

1. **SMS** is direct payment, where money is charged in 1 event.
2. **DMS** is delayed payment, requires 2 events:
    - First event blocks money on the card
    - Second event takes money (It can be carried out when product is shipped to the customer, for example)

In every 24 hour the merchant must send the close the business day request to bank server

## Usage

There are several methods you need to know:

- `SMSTransaction($amount, $currency = 981, $description = '', $language = 'GE')`
- `DMSAuthorization($amount, $currency = 981, $description = '', $language = 'GE')`
- `DMSTransaction($txn_id, $amount, $currency = 981, $description = '', $language = 'GE')`
- `getTransactionResult($txn_id)`
- `reverseTransaction($txn_id, $amount = '', $suspected_fraud = '')`
- `refundTransaction($txn_id)`
- `creditTransaction($txn_id, $amount = '')`
- `closeDay()`

## Examples

In your `routes.php` create route:

```php
Route::get('payment/{status}', function($status) {
    if ($status == 'success') {
        return TBC::getTransactionResult(request('trans_id'));
    }
    
    return 'FAIL!';
})->where('status', 'success|fail');

Route::get('pay', function() {
    return view('payment.tbc', [
        'start' => TBC::SMSTransaction(1)
    ]);
});
```

Create `payment/tbc.blade.php`. It should look like:

```php
<!doctype html>
<html>
<head>
    <title>TBC</title>
</head>
<body>

@if(isset($start['error']))
    <h2>Error:</h2>
    <h1>{{ $start['error'] }}</h1>
@elseif(isset($start['TRANSACTION_ID']))
    <form name="returnform" id="Pay" action="https://securepay.ufc.ge/ecomm2/ClientHandler" method="POST">
        <input type="hidden" name="trans_id" value="{{ $start['TRANSACTION_ID'] }}">

        <noscript>
            <center>Please click the submit button below.<br>
            <input type="submit" name="submit" value="Submit"></center>
        </noscript>
    </form>
    
    <script>
        window.onload = document.forms.Pay.submit;
    </script>
@endif

</body>
</html>
```

## Codes

| Key | Value             | Description                                                                           |
|-----|-------------------|---------------------------------------------------------------------------------------|
| 000 | Approved          | Approved                                                                              |
| 001 | Approved with ID  | Approved, honour with identification                                                  |
| 002 | Approved          | Approved for partial amount                                                           |
| 003 | Approved          | Approved for VIP                                                                      |
| 004 | Approved          | Approved, update track 3                                                              |
| 005 | Approved          | Approved, account type specified by card issuer                                       |
| 006 | Approved          | Approved for partial amount, account type specified by card issuer                    |
| 007 | Approved          | Approved, update ICC                                                                  |
| 100 | Decline           | Decline (general, no comments)                                                        |
| 101 | Decline           | Decline, expired card                                                                 |
| 102 | Decline           | Decline, suspected fraud                                                              |
| 103 | Decline           | Decline, card acceptor contact acquirer                                               |
| 104 | Decline           | Decline, restricted card                                                              |
| 105 | Decline           | Decline, card acceptor call acquirer's security department                            |
| 106 | Decline           | Decline, allowable PIN tries exceeded                                                 |
| 107 | Decline           | Decline, refer to card issuer                                                         |
| 108 | Decline           | Decline, refer to card issuer's special conditions                                    |
| 109 | Decline           | Decline, invalid merchant                                                             |
| 110 | Decline           | Decline, invalid amount                                                               |
| 111 | Decline           | Decline, invalid card number                                                          |
| 112 | Decline           | Decline, PIN data required                                                            |
| 113 | Decline           | Decline, unacceptable fee                                                             |
| 114 | Decline           | Decline, no account of type requested                                                 |
| 115 | Decline           | Decline, requested function not supported                                             |
| 116 | Decline, no funds | Decline, not sufficient funds                                                         |
| 117 | Decline           | Decline, incorrect PIN                                                                |
| 118 | Decline           | Decline, no card record                                                               |
| 119 | Decline           | Decline, transaction not permitted to cardholder                                      |
| 120 | Decline           | Decline, transaction not permitted to terminal                                        |
| 121 | Decline           | Decline, exceeds withdrawal amount limit                                              |
| 122 | Decline           | Decline, security violation                                                           |
| 123 | Decline           | Decline, exceeds withdrawal frequency limit                                           |
| 124 | Decline           | Decline, violation of law                                                             |
| 125 | Decline           | Decline, card not effective                                                           |
| 126 | Decline           | Decline, invalid PIN block                                                            |
| 127 | Decline           | Decline, PIN length error                                                             |
| 128 | Decline           | Decline, PIN kay synch error                                                          |
| 129 | Decline           | Decline, suspected counterfeit card                                                   |
| 180 | Decline           | Decline, by cardholders wish                                                          |
| 200 | Pick-up           | Pick-up (general, no comments)                                                        |
| 201 | Pick-up           | Pick-up, expired card                                                                 |
| 202 | Pick-up           | Pick-up, suspected fraud                                                              |
| 203 | Pick-up           | Pick-up, card acceptor contact card acquirer                                          |
| 204 | Pick-up           | Pick-up, restricted card                                                              |
| 205 | Pick-up           | Pick-up, card acceptor call acquirer's security department                            |
| 206 | Pick-up           | Pick-up, allowable PIN tries exceeded                                                 |
| 207 | Pick-up           | Pick-up, special conditions                                                           |
| 208 | Pick-up           | Pick-up, lost card                                                                    |
| 209 | Pick-up           | Pick-up, stolen card                                                                  |
| 210 | Pick-up           | Pick-up, suspected counterfeit card                                                   |
| 300 | Call acquirer     | Status message: file action successful                                                |
| 301 | Call acquirer     | Status message: file action not supported by receiver                                 |
| 302 | Call acquirer     | Status message: unable to locate record on file                                       |
| 303 | Call acquirer     | Status message: duplicate record, old record replaced                                 |
| 304 | Call acquirer     | Status message: file record field edit error                                          |
| 305 | Call acquirer     | Status message: file locked out                                                       |
| 306 | Call acquirer     | Status message: file action not successful                                            |
| 307 | Call acquirer     | Status message: file data format error                                                |
| 308 | Call acquirer     | Status message: duplicate record, new record rejected                                 |
| 309 | Call acquirer     | Status message: unknown file                                                          |
| 400 | Accepted          | Accepted (for reversal)                                                               |
| 499 | Approved          | Approved, no original message data                                                    |
| 500 | Call acquirer     | Status message: reconciled, in balance                                                |
| 501 | Call acquirer     | Status message: reconciled, out of balance                                            |
| 502 | Call acquirer     | Status message: amount not reconciled, totals provided                                |
| 503 | Call acquirer     | Status message: totals for reconciliation not available                               |
| 504 | Call acquirer     | Status message: not reconciled, totals provided                                       |
| 600 | Accepted          | Accepted (for administrative info)                                                    |
| 601 | Call acquirer     | Status message: impossible to trace back original transaction                         |
| 602 | Call acquirer     | Status message: invalid transaction reference number                                  |
| 603 | Call acquirer     | Status message: reference number/PAN incompatible                                     |
| 604 | Call acquirer     | Status message: POS photograph is not available                                       |
| 605 | Call acquirer     | Status message: requested item supplied                                               |
| 606 | Call acquirer     | Status message: request cannot be fulfilled - required documentation is not available |
| 680 | List ready        | List ready                                                                            |
| 681 | List not ready    | List not ready                                                                        |
| 700 | Accepted          | Accepted (for fee collection)                                                         |
| 800 | Accepted          | Accepted (for network management)                                                     |
| 900 | Accepted          | Advice acknowledged, no financial liability accepted                                  |
| 901 | Accepted          | Advice acknowledged, finansial liability accepted                                     |
| 902 | Call acquirer     | Decline reason message: invalid transaction                                           |
| 903 | Call acquirer     | Status message: re-enter transaction                                                  |
| 904 | Call acquirer     | Decline reason message: format error                                                  |
| 905 | Call acquirer     | Decline reason message: acqiurer not supported by switch                              |
| 906 | Call acquirer     | Decline reason message: cutover in process                                            |
| 907 | Call acquirer     | Decline reason message: card issuer or switch inoperative                             |
| 908 | Call acquirer     | Decline reason message: transaction destination cannot be found for routing           |
| 909 | Call acquirer     | Decline reason message: system malfunction                                            |
| 910 | Call acquirer     | Decline reason message: card issuer signed off                                        |
| 911 | Call acquirer     | Decline reason message: card issuer timed out                                         |
| 912 | Call acquirer     | Decline reason message: card issuer unavailable                                       |
| 913 | Call acquirer     | Decline reason message: duplicate transmission                                        |
| 914 | Call acquirer     | Decline reason message: not able to trace back to original transaction                |
| 915 | Call acquirer     | Decline reason message: reconciliation cutover or checkpoint error                    |
| 916 | Call acquirer     | Decline reason message: MAC incorrect                                                 |
| 917 | Call acquirer     | Decline reason message: MAC key sync error                                            |
| 918 | Call acquirer     | Decline reason message: no communication keys available for use                       |
| 919 | Call acquirer     | Decline reason message: encryption key sync error                                     |
| 920 | Call acquirer     | Decline reason message: security software/hardware error - try again                  |
| 921 | Call acquirer     | Decline reason message: security software/hardware error - no action                  |
| 922 | Call acquirer     | Decline reason message: message number out of sequence                                |
| 923 | Call acquirer     | Status message: request in progress                                                   |
| 950 | Not accepted      | Decline reason message: violation of business arrangement                             |
| XXX | Undefined         | Code to be replaced by card status code or stoplist insertion reason code             |

## Config

Publish TBC config file using command:

```
php artisan vendor:publish
```

Created file `config\tbc.php`. Inside you can change configuration as you wish.

## License

laravel-tbcpay is licensed under a  [MIT License](https://github.com/zgabievi/laravel-tbcpay/blob/master/LICENSE).

## TODO
- [ ] Take response codes from README and put somewhere else
- [ ] Create some tests to check full functionality
- [ ] Make TBC facade more Model like
- [ ] Create payment view in vendor, so that developer won't have to do it manually

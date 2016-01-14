# TBC

[![TBC](http://i.imgsafe.org/09cb0be.jpg)](https://github.com/zgabievi/TBC)

[![Latest Stable Version](https://poser.pugx.org/zgabievi/TBC/version.png)](https://packagist.org/packages/zgabievi/tbc)
[![Total Downloads](https://poser.pugx.org/zgabievi/TBC/d/total.png)](https://packagist.org/packages/zgabievi/tbc)
[![License](https://poser.pugx.org/zgabievi/TBC/license)](https://packagist.org/packages/zgabievi/tbc)

TBC Payment System for [Laravel 5.*](http://laravel.com/)

Inspired from [tbcpay-php](https://github.com/wearede/tbcpay-php), created by [Sandro Dzneladze](https://github.com/sandrodz)

## Table of Contents
- [Installation](#installation)
    - [Composer](#composer)
    - [Laravel](#laravel)
- [Documentation](#documentation)
- [Usage](#usage)
- [Examples](#examples)
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
- `GetTransactionResult($txn_id)`
- `ReverseTransaction($txn_id, $amount = '', $suspected_fraud = '')`
- `RefundTransaction($txn_id)`
- `CreditTransaction($txn_id, $amount = '')`
- `CloseDay()`

## Examples

In your `routes.php` create route:

```php
Route::get('payment/{status}', function($status) {
    if ($status == 'success') {
        return TBC::GetTransactionResult(request('trans_id'));
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


## Config

Publish TBC config file using command:

```
php artisan vendor:publish
```

Created file `config\tbc.php`. Inside you can change configuration as you wish.

## License

TBC is an open-sourced laravel package licensed under the MIT license

## TODO
- [ ] Create tests

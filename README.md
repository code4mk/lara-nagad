<p align="center" ><img src="https://raw.githubusercontent.com/code4mk/lara-nagad/master/nagad%20payment.png"></p>

# lara-nagad `Bangladesh Nagad`
Laravel Nagad payment `BD`

# Installation

```bash
composer require code4mk/lara-nagad
```

# Setup

## 1 ) vendor publish (config)

```bash
php artisan vendor:publish --provider="Code4mk\Nagad\NagadServiceProvider" --tag=config
```

## 1.1 ) if you are using Laravel before version 5.4, manually register the service provider in your config/app.php file

```php
Code4mk\Nagad\NagadServiceProvider::class
```

## 2 ) Config setup

* `config/nagad.php`

```php
<?php

return [
    'sandbox_mode' => env('NAGAD_MODE', 'sandbox'),
    'merchant_id' => env('NAGAD_MERCHANT_ID','683002007104225'),
    'merchant_number' => env('NAGAD_MERCHANT_NUMBER','01711428036'),
    'callback_url' => env('NAGAD_CALLBACK_URL', 'http://127.0.0.1:8000/nagad/callback'),
    'public_key' => env('NAGAD_PUBLIC_KEY',''),
    'private_key' => env('NAGAD_PRIVATE_KEY','')
];
```

# env setup

```bash
NAGAD_MERCHANT_ID=683002007104225
NAGAD_MERCHANT_NUMBER=01711428036
NAGAD_CALLBACK_URL=http://127.0.0.1:8000/nagad/callback
NAGAD_MODE=sandbox // sandbox or live
NAGAD_PUBLIC_KEY="" //sandbox <optional>
NAGAD_PRIVATE_KEY=""  // sandbox <optional>
```

# Usage

## get callback url

```php
<?php
use NagadPayment;

$redirectUrl = NagadPayment::tnxID($id)
             ->amount($amount)
             ->getRedirectUrl();
return $redirectUrl;
```

## verify payment // callback

```php
<?php
use NagadPayment;

$verify = (object) NagadPayment::verify();
if($verify->status === 'Success'){
    $order = json_decode($verify->additionalMerchantInfo);
    $order_id = $order->tnx_id;
    // your functional task with order_id
}
if ($verify->status === 'Aborted') {
    dd($verify);
    // redirect or other what you want
}
dd($verify);

```

# Note:

`~Sandbox`

* Need a merchant account.
* Register a Nagad number and need sandbox balance (contact with nagad)

`~ Live`

* Need a merchant account (live server)
* Contact with Nagad and provide your live server ip address.
* provide support id ($sid) the nagad office

# Live mode tips 

`Sandbox works fine but when you deploy your project on server you can't get any response and don't work payment system`

## How to enable nagad gateway on server 

* Contact with nagad, provide your ip and support ID  which you will get from temporary route `get-support-id` . Nagad will be white-listed your ip and approve your merchant. Now your nagad gateway work properly on server.

~ temporary route (copy and paste)

```php
Route::get('get-support-id',function(){
    $sid = NagadPayment::tnxID(1)
                 ->amount(100)
                 ->getSupportID();
    return $sid;
})
```

# Demo

* [Lara Nagad Demo](https://github.com/code4mk/lara-nagad-demo)

# Any query

* hiremostafa@gmail.com

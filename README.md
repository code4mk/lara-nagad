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

# Any query

* hiremostafa@gmail.com

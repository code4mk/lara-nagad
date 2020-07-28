# lara-nagad `Bangladesh Nagad`
Laravel Nagad payment `BD`

# Installation

```bash
composer require code4mk/lara-nagad
```

# Setup

## 1 ) vendor publish (config)

```bash
php artisan vendor:publish --provider="Code4mk\NagadServiceProvider" --tag=config
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

## verify payment

```php
<?php
use NagadPayment;

$data = NagadPayment::verify();
return $data;
```

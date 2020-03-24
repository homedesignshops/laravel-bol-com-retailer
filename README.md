# Laravel Bol.com Retailer API
[![Latest Version on Packagist](https://img.shields.io/packagist/v/homedesignshops/laravel-bol-com-retailer.svg?style=flat-square)](https://packagist.org/packages/homedesignshops/laravel-bol-com-retailer)
[![Build Status](https://img.shields.io/travis/homedesignshops/laravel-bol-com-retailer/master.svg?style=flat-square)](https://travis-ci.org/homedesignshops/laravel-bol-com-retailer)
[![Quality Score](https://img.shields.io/scrutinizer/g/homedesignshops/laravel-bol-com-retailer.svg?style=flat-square)](https://scrutinizer-ci.com/g/homedesignshops/laravel-bol-com-retailer)
[![Total Downloads](https://img.shields.io/packagist/dt/homedesignshops/laravel-bol-com-retailer.svg?style=flat-square)](https://packagist.org/packages/homedesignshops/laravel-bol-com-retailer)

A wrapper for the Bol.com Retailer API from [Picger](https://github.com/picqer/bol-retailer-php-client)'s Bol.com package.

## Installation

You can install the package via composer:

```bash
composer require homedesignshops/laravel-bol-com-retailer
```

Configure the `.env` file
```dotenv
BOL_COM_DEMO_MODE=true
BOL_COM_CLIENT_ID={CLIENT_ID_HERE}
BOL_COM_CLIENT_SECRET={CLIENT_SECRET_HERE}
```

Publish the config file
```bash
php artisan vendor:publish --provider="HomeDesignShops/LaravelBolComRetail/BolComRetailerServiceProvider" --tag="config"
```

You're ready to use this package

## Usage

### Get all open orders
``` php
$orders = BolComRetailer::getOpenOrders();
```

### Get order
``` php
$order = BolComRetailer::getOrder($orderId);
```

### Ship order item
``` php
$processStatus = BolComRetailer::shipOrderItem(OrderItem $orderItem, Transport $transport);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email kevin@homedesignshops.nl instead of using the issue tracker.

## Credits

- [Kevin Koenen](https://github.com/kevinkoenen.nl)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

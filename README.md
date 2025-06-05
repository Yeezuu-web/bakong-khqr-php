# Bakong KHQR PHP

A PHP package for generating and parsing Bakong KHQR (Cambodian QR payment) codes.

## Installation

You can install the package via Composer:

```bash
composer require piseth/bakong-khqr-php
```

## Usage

### Basic Usage

```php
use Piseth\BakongKhqr\KhqrGenerator;

$generator = new KhqrGenerator();
$qrData = $generator->generate([
    'amount' => '100.00',
    'currency' => 'KHR',
    'merchant_name' => 'Your Merchant Name',
    'merchant_city' => 'Phnom Penh'
]);

echo $qrData;
```

### Parsing KHQR Data

```php
use Piseth\BakongKhqr\KhqrParser;

$parser = new KhqrParser();
$data = $parser->parse($qrString);
```

## Features

- Generate Bakong KHQR codes
- Parse existing KHQR codes
- Validate KHQR data format
- Support for both KHR and USD currencies
- PSR-4 autoloading
- Comprehensive tests

## Requirements

- PHP 8.0 or higher

## Testing

```bash
composer test
```

## Code Style

```bash
composer cs-check
composer cs-fix
```

## Static Analysis

```bash
composer analyse
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email your-email@example.com instead of using the issue tracker.

## Credits

- [Piseth](https://github.com/your-username)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

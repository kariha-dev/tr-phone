# TR Phone

[English](README.md) | [Türkçe](README.tr.md)

TR Phone is a small, framework-agnostic PHP toolkit for Turkish phone number validation, Türkiye phone number normalization, +90 phone formatting, E.164 conversion, mobile number checks, and landline checks.

It is built for web forms, CRM systems, appointment software, e-commerce projects, WhatsApp integrations, customer databases, API payloads, and import cleanup jobs that need reliable Turkish phone handling without an external service.

![PHP](https://img.shields.io/badge/php-%5E8.1-777bb4.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![CI](https://github.com/kariha-dev/tr-phone/actions/workflows/ci.yml/badge.svg)

## What is TR Phone?

TR Phone focuses only on Türkiye / +90 numbers. It validates against Turkish numbering ranges documented by BTK and formats valid numbers consistently:

- E.164: `+905550000000`
- National: `0555 000 00 00`
- International: `+90 555 000 00 00`
- Compact national significant digits: `5550000000`

The package runs locally. It does not call an API, does not use a database, and does not transmit phone-number data to Kariha or any third party.

## Features

- Parse common Turkish phone number inputs such as `05550000000`, `5550000000`, `+905550000000`, `00905550000000`, `(0555) 000 00 00`, and `0555-000-00-00`
- Validate recognized Turkish mobile and geographic landline ranges
- Normalize to canonical 10-digit national significant numbers
- Format as E.164, national, international, or compact
- Distinguish mobile and landline numbers
- Return prefix-based original allocation metadata for mobile prefixes
- Throw meaningful exceptions or use safe `tryParse()` calls
- No Laravel, Symfony, database, HTTP, or external API dependency

## Installation

```bash
composer require kariha/tr-phone
```

## Quick Start

```php
<?php

use Kariha\TrPhone\PhoneNumber;

$phone = PhoneNumber::parse('0555 000 00 00');

echo $phone->e164();         // +905550000000
echo $phone->national();     // 0555 000 00 00
echo $phone->international(); // +90 555 000 00 00
echo $phone->digits();       // 5550000000
```

## Validation

```php
use Kariha\TrPhone\PhoneNumber;

if (PhoneNumber::tryParse($input) !== null) {
    // The input is a recognized Turkish phone number.
}
```

You can also use the facade-style helper:

```php
use Kariha\TrPhone\TrPhone;

if (TrPhone::validate('+90 532 123 45 67')) {
    // Valid Turkish phone number.
}
```

## Normalization

```php
use Kariha\TrPhone\TrPhone;

echo TrPhone::normalize('(0555) 000 00 00'); // 5550000000
```

TR Phone handles leading `0`, `+90`, `0090`, whitespace, parentheses, hyphens, dots, and pasted formatted values. It rejects unsupported international numbers instead of silently converting them.

## Formatting

```php
use Kariha\TrPhone\Format;
use Kariha\TrPhone\PhoneNumber;

$phone = PhoneNumber::parse('0555 000 00 00');

echo $phone->format(Format::E164);          // +905550000000
echo $phone->format(Format::NATIONAL);      // 0555 000 00 00
echo $phone->format(Format::INTERNATIONAL); // +90 555 000 00 00
echo $phone->format(Format::COMPACT);       // 5550000000
```

## Mobile Numbers

```php
$phone = PhoneNumber::parse('0532 123 45 67');

$phone->isMobile(); // true
```

Mobile validation is based on BTK mobile numbering ranges such as `50X`, `53X`, `54X`, selected `55X`, and other documented mobile-service prefixes.

## Landline Numbers

```php
$phone = PhoneNumber::parse('0212 123 45 67');

$phone->isLandline(); // true
$phone->areaName();   // Istanbul Europe
```

Geographic numbers are recognized through BTK 2XX, 3XX, and 4XX area codes.

## Prefix / Operator Information

```php
$phone = PhoneNumber::parse('0532 123 45 67');

echo $phone->prefix();         // 532
echo $phone->prefixOperator(); // Turkcell
```

`prefixOperator()` and `prefixAllocation()` return prefix-based original allocation information. They do not identify the subscriber's current carrier.

## Number Portability Warning

Türkiye supports mobile number portability. A mobile prefix may show the original allocation, but it is not proof of the current operator. Do not use this package for billing, routing, or carrier-sensitive decisions that require current network data.

## Error Handling

```php
use Kariha\TrPhone\Exception\InvalidPhoneNumberException;
use Kariha\TrPhone\PhoneNumber;

try {
    $phone = PhoneNumber::parse($input);
} catch (InvalidPhoneNumberException $exception) {
    // Show a user-friendly validation message or log the reason.
}
```

Use `tryParse()` when you prefer a non-throwing API:

```php
$phone = PhoneNumber::tryParse($input);

if ($phone === null) {
    // Invalid or unsupported number.
}
```

## Examples

More copy-paste examples are available in [docs/examples.md](docs/examples.md).

## API Reference

- `PhoneNumber::parse(string $input): PhoneNumber`
- `PhoneNumber::tryParse(string $input): ?PhoneNumber`
- `PhoneNumber::isValidInput(string $input): bool`
- `PhoneNumber::digits(): string`
- `PhoneNumber::e164(): string`
- `PhoneNumber::national(): string`
- `PhoneNumber::international(): string`
- `PhoneNumber::format(Format|string $format): string`
- `PhoneNumber::isMobile(): bool`
- `PhoneNumber::isLandline(): bool`
- `PhoneNumber::prefix(): string`
- `PhoneNumber::prefixOperator(): ?string`
- `PhoneNumber::prefixAllocation(): ?string`
- `PhoneNumber::areaName(): ?string`
- `TrPhone::validate(string $input): bool`
- `TrPhone::normalize(string $input): string`
- `TrPhone::format(string $input, Format|string $format = Format::E164): string`

## Laravel / Symfony / Plain PHP

TR Phone is plain PHP. Use it in Laravel, Symfony, Slim, Laminas, WordPress, custom PHP applications, queue jobs, import scripts, or any Composer-based project.

## Requirements

- PHP 8.1 or higher
- Composer

## Testing

```bash
composer install
composer test
composer analyse
composer cs
composer check
```

## Data Source

Numbering data is maintained in `src/Data/TurkishNumberingPlan.php` and is based on BTK's public Turkish numbering plan pages, verified on 2026-09-23:

- https://www.btk.gov.tr/genel-numaralandirma-plani
- https://www.btk.gov.tr/ulusal-numaralandirma-plani

If BTK changes the plan, update the data file and add tests for the new ranges.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md). Do not post real customer phone numbers in issues; mask personal data such as `0555 *** ** 78`.

## Security

Please read [SECURITY.md](SECURITY.md). Security reports can be sent to `info@kariha.net`.

## License

TR Phone is open-source software licensed under the [MIT license](LICENSE).

## About Kariha

Kariha Web Agency develops custom web software, hosting infrastructure, and digital solutions since 2003.

Website: https://www.kariha.net/

For commercial integrations or custom development: https://www.kariha.net/iletisim

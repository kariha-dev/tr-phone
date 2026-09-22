# Examples

## CRM Import Cleanup

```php
use Kariha\TrPhone\PhoneNumber;

$rows = [
    ['name' => 'Example Customer', 'phone' => '(0555) 000 00 00'],
];

foreach ($rows as $row) {
    $phone = PhoneNumber::tryParse($row['phone']);

    if ($phone === null) {
        continue;
    }

    $row['phone_e164'] = $phone->e164();
}
```

## HTML Form Validation

```php
use Kariha\TrPhone\PhoneNumber;

$phone = PhoneNumber::tryParse($_POST['phone'] ?? '');

if ($phone === null) {
    $errors['phone'] = 'Please enter a valid Turkish phone number.';
}
```

## WhatsApp-Compatible Normalization

```php
use Kariha\TrPhone\PhoneNumber;

$phone = PhoneNumber::parse('0555 000 00 00');

$payload = [
    'to' => $phone->e164(),
    'message' => 'Your appointment is confirmed.',
];
```

## Database Storage Recommendation

Store canonical values, not the user's original formatting:

```php
$phone = PhoneNumber::parse($input);

$customer->phone_e164 = $phone->e164();
$customer->phone_digits = $phone->digits();
```

For most integrations, E.164 is the best primary storage format because it keeps the country code and is widely understood by APIs.

## API Payload Normalization

```php
use Kariha\TrPhone\Format;
use Kariha\TrPhone\TrPhone;

$payload['phone'] = TrPhone::format($payload['phone'], Format::E164);
```

## Prefix Allocation Display

```php
$phone = PhoneNumber::parse('0532 123 45 67');

echo $phone->prefixOperator(); // Turkcell
```

This is original prefix allocation information. It is not the current carrier because mobile number portability is available in Türkiye.

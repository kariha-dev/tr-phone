# TR Phone

[English](README.md) | [Türkçe](README.tr.md)

TR Phone; PHP projelerinde Türkiye telefon numarası doğrulama, telefon numarası normalizasyonu, +90 formatlama, E.164 dönüşümü, cep telefonu ve sabit hat kontrolü için geliştirilmiş küçük ve framework bağımsız bir pakettir.

Web formları, CRM sistemleri, randevu yazılımları, e-ticaret projeleri, WhatsApp entegrasyonları, müşteri veritabanları, API payload normalizasyonu ve toplu veri temizleme süreçleri için uygundur.

![PHP](https://img.shields.io/badge/php-%5E8.1-777bb4.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![CI](https://github.com/kariha-dev/tr-phone/actions/workflows/ci.yml/badge.svg)

## TR Phone nedir?

TR Phone yalnızca Türkiye / +90 numaralarına odaklanır. BTK tarafından yayımlanan numaralandırma planındaki doğrulanabilir aralıklara göre kontrol yapar ve geçerli numaraları tutarlı biçimde formatlar:

- E.164: `+905550000000`
- Ulusal format: `0555 000 00 00`
- Uluslararası format: `+90 555 000 00 00`
- Kompakt ulusal anlamlı rakamlar: `5550000000`

Paket yerel olarak çalışır. API çağrısı yapmaz, veritabanı kullanmaz ve telefon numarası bilgisini Kariha'ya veya üçüncü taraflara göndermez.

## Özellikler

- `05550000000`, `5550000000`, `+905550000000`, `00905550000000`, `(0555) 000 00 00` ve `0555-000-00-00` gibi yaygın girişleri ayrıştırır
- Tanınan Türkiye cep telefonu ve coğrafi sabit hat aralıklarını doğrular
- Numarayı standart 10 haneli ulusal anlamlı rakamlara normalleştirir
- E.164, ulusal, uluslararası ve kompakt format çıktıları üretir
- Cep telefonu ve sabit hat ayrımı yapar
- Cep telefonu prefix bilgisi için tahsis bilgisini döndürür
- Anlamlı istisnalar veya güvenli `tryParse()` kullanımı sunar
- Laravel, Symfony, veritabanı, HTTP veya harici API bağımlılığı yoktur

## Kurulum

```bash
composer require kariha/tr-phone
```

## Hızlı Başlangıç

```php
<?php

use Kariha\TrPhone\PhoneNumber;

$phone = PhoneNumber::parse('0555 000 00 00');

echo $phone->e164();          // +905550000000
echo $phone->national();      // 0555 000 00 00
echo $phone->international(); // +90 555 000 00 00
echo $phone->digits();        // 5550000000
```

## Doğrulama

```php
use Kariha\TrPhone\PhoneNumber;

if (PhoneNumber::tryParse($input) !== null) {
    // Girdi tanınan bir Türkiye telefon numarasıdır.
}
```

Kolay kullanım sınıfı:

```php
use Kariha\TrPhone\TrPhone;

if (TrPhone::validate('+90 532 123 45 67')) {
    // Geçerli Türkiye telefon numarası.
}
```

## Normalizasyon

```php
use Kariha\TrPhone\TrPhone;

echo TrPhone::normalize('(0555) 000 00 00'); // 5550000000
```

TR Phone baştaki `0`, `+90`, `0090`, boşluk, parantez, tire, nokta ve kopyalanmış formatlı değerleri işler. Türkiye dışı uluslararası numaraları sessizce dönüştürmez; reddeder.

## Formatlama

```php
use Kariha\TrPhone\Format;
use Kariha\TrPhone\PhoneNumber;

$phone = PhoneNumber::parse('0555 000 00 00');

echo $phone->format(Format::E164);          // +905550000000
echo $phone->format(Format::NATIONAL);      // 0555 000 00 00
echo $phone->format(Format::INTERNATIONAL); // +90 555 000 00 00
echo $phone->format(Format::COMPACT);       // 5550000000
```

## Cep Telefonu

```php
$phone = PhoneNumber::parse('0532 123 45 67');

$phone->isMobile(); // true
```

Cep telefonu doğrulaması BTK planındaki `50X`, `53X`, `54X`, belirli `55X` ve diğer belgelenmiş mobil hizmet prefixlerine göre yapılır.

## Sabit Hat

```php
$phone = PhoneNumber::parse('0212 123 45 67');

$phone->isLandline(); // true
$phone->areaName();   // Istanbul Europe
```

Coğrafi numaralar BTK 2XX, 3XX ve 4XX alan kodları üzerinden tanınır.

## Prefix / Operatör Bilgisi

```php
$phone = PhoneNumber::parse('0532 123 45 67');

echo $phone->prefix();         // 532
echo $phone->prefixOperator(); // Turkcell
```

`prefixOperator()` ve `prefixAllocation()` yalnızca prefix bazlı ilk tahsis bilgisini döndürür. Abonenin güncel operatörünü garanti etmez.

## Numara Taşınabilirliği Uyarısı

Türkiye'de mobil numara taşınabilirliği vardır. Bir cep telefonu prefixi, numaranın ilk tahsis edildiği işletmeciyi gösterebilir; fakat hattın güncel operatörünü kanıtlamaz. Güncel şebeke bilgisinin gerekli olduğu faturalama, yönlendirme veya operatör hassasiyetli kararlar için bu paketi tek kaynak olarak kullanmayın.

## Hata Yönetimi

```php
use Kariha\TrPhone\Exception\InvalidPhoneNumberException;
use Kariha\TrPhone\PhoneNumber;

try {
    $phone = PhoneNumber::parse($input);
} catch (InvalidPhoneNumberException $exception) {
    // Kullanıcı dostu bir doğrulama mesajı gösterin veya sebebi loglayın.
}
```

İstisna fırlatmayan kullanım:

```php
$phone = PhoneNumber::tryParse($input);

if ($phone === null) {
    // Geçersiz veya desteklenmeyen numara.
}
```

## Örnekler

Daha fazla örnek için [docs/examples.md](docs/examples.md) dosyasına bakabilirsiniz.

## API Özeti

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

TR Phone düz PHP paketidir. Laravel, Symfony, Slim, Laminas, WordPress, özel PHP uygulamaları, kuyruk işleri, import scriptleri veya Composer kullanan herhangi bir projede kullanılabilir.

## Gereksinimler

- PHP 8.1 veya üzeri
- Composer

## Test

```bash
composer install
composer test
composer analyse
composer cs
composer check
```

## Veri Kaynağı

Numaralandırma verisi `src/Data/TurkishNumberingPlan.php` içinde tutulur ve 2026-09-23 tarihinde doğrulanan BTK numaralandırma planı sayfalarına dayanır:

- https://www.btk.gov.tr/genel-numaralandirma-plani
- https://www.btk.gov.tr/ulusal-numaralandirma-plani

BTK planında değişiklik olursa veri dosyası ve ilgili testler güncellenmelidir.

## Katkı

Lütfen [CONTRIBUTING.md](CONTRIBUTING.md) dosyasını okuyun. GitHub issue'larında gerçek müşteri telefon numarası paylaşmayın; kişisel verileri `0555 *** ** 78` gibi maskeleyin.

## Güvenlik

Lütfen [SECURITY.md](SECURITY.md) dosyasını okuyun. Güvenlik bildirimleri `info@kariha.net` adresine gönderilebilir.

## Lisans

TR Phone [MIT lisansı](LICENSE) ile yayımlanan açık kaynak yazılımdır.

## Kariha Hakkında

Kariha Web Agency, 2003 yılından beri özel web yazılımları, hosting altyapısı ve dijital çözümler geliştirir.

Web sitesi: https://www.kariha.net/

Ticari entegrasyonlar veya özel yazılım geliştirme için: https://www.kariha.net/iletisim

<?php

declare(strict_types=1);

namespace Kariha\TrPhone\Tests;

use Kariha\TrPhone\Data\TurkishNumberingPlan;
use Kariha\TrPhone\Exception\InvalidPhoneNumberException;
use Kariha\TrPhone\Format;
use Kariha\TrPhone\PhoneNumber;
use Kariha\TrPhone\PhoneType;
use Kariha\TrPhone\TrPhone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PhoneNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function validMobileInputs(): iterable
    {
        yield 'national compact' => ['05550000000'];
        yield 'without trunk zero' => ['5550000000'];
        yield 'e164' => ['+905550000000'];
        yield 'international 00' => ['00905550000000'];
        yield 'spaced international' => ['+90 555 000 00 00'];
        yield 'spaced national' => ['0555 000 00 00'];
        yield 'parentheses' => ['(0555) 000 00 00'];
        yield 'hyphenated' => ['0555-000-00-00'];
        yield 'dotted' => ['0555.000.00.00'];
    }

    #[DataProvider('validMobileInputs')]
    public function testParsesMobileNumbers(string $input): void
    {
        $phone = PhoneNumber::parse($input);

        self::assertTrue($phone->isValid());
        self::assertTrue($phone->isMobile());
        self::assertFalse($phone->isLandline());
        self::assertSame(PhoneType::MOBILE, $phone->type());
        self::assertSame('5550000000', $phone->digits());
        self::assertSame('555', $phone->prefix());
        self::assertSame('Turk Telekom', $phone->prefixOperator());
    }

    public function testParsesLandlineNumbers(): void
    {
        $phone = PhoneNumber::parse('0212 123 45 67');

        self::assertTrue($phone->isLandline());
        self::assertFalse($phone->isMobile());
        self::assertSame(PhoneType::LANDLINE, $phone->type());
        self::assertSame('2121234567', $phone->digits());
        self::assertSame('Istanbul Europe', $phone->areaName());
        self::assertNull($phone->prefixOperator());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function supportedGeographicAreaCodes(): iterable
    {
        $areaNames = [
            '212' => 'Istanbul Europe',
            '216' => 'Istanbul Anatolia',
            '222' => 'Eskisehir',
            '224' => 'Bursa',
            '226' => 'Yalova',
            '228' => 'Bilecik',
            '232' => 'Izmir',
            '236' => 'Manisa',
            '242' => 'Antalya',
            '246' => 'Isparta',
            '248' => 'Burdur',
            '252' => 'Mugla',
            '256' => 'Aydin',
            '258' => 'Denizli',
            '262' => 'Kocaeli',
            '264' => 'Sakarya',
            '266' => 'Balikesir',
            '272' => 'Afyonkarahisar',
            '274' => 'Kutahya',
            '276' => 'Usak',
            '282' => 'Tekirdag',
            '284' => 'Edirne',
            '286' => 'Canakkale',
            '288' => 'Kirklareli',
            '312' => 'Ankara',
            '318' => 'Kirikkale',
            '322' => 'Adana',
            '324' => 'Mersin',
            '326' => 'Hatay',
            '328' => 'Osmaniye',
            '332' => 'Konya',
            '338' => 'Karaman',
            '342' => 'Gaziantep',
            '344' => 'Kahramanmaras',
            '346' => 'Sivas',
            '348' => 'Kilis',
            '352' => 'Kayseri',
            '354' => 'Yozgat',
            '356' => 'Tokat',
            '358' => 'Amasya',
            '362' => 'Samsun',
            '364' => 'Corum',
            '366' => 'Kastamonu',
            '368' => 'Sinop',
            '370' => 'Karabuk',
            '372' => 'Zonguldak',
            '374' => 'Bolu',
            '376' => 'Cankiri',
            '378' => 'Bartin',
            '380' => 'Duzce',
            '382' => 'Aksaray',
            '384' => 'Nevsehir',
            '386' => 'Kirsehir',
            '388' => 'Nigde',
            '412' => 'Diyarbakir',
            '414' => 'Sanliurfa',
            '416' => 'Adiyaman',
            '422' => 'Malatya',
            '424' => 'Elazig',
            '426' => 'Bingol',
            '428' => 'Tunceli',
            '432' => 'Van',
            '434' => 'Bitlis',
            '436' => 'Mus',
            '438' => 'Hakkari',
            '442' => 'Erzurum',
            '446' => 'Erzincan',
            '452' => 'Ordu',
            '454' => 'Giresun',
            '456' => 'Gumushane',
            '458' => 'Bayburt',
            '462' => 'Trabzon',
            '464' => 'Rize',
            '466' => 'Artvin',
            '472' => 'Agri',
            '474' => 'Kars',
            '476' => 'Igdir',
            '478' => 'Ardahan',
            '482' => 'Mardin',
            '484' => 'Siirt',
            '486' => 'Sirnak',
            '488' => 'Batman',
        ];

        foreach ($areaNames as $areaCode => $areaName) {
            yield $areaName => [(string) $areaCode, $areaName];
        }
    }

    #[DataProvider('supportedGeographicAreaCodes')]
    public function testAllSupportedGeographicAreaCodesAreRecognized(string $areaCode, string $areaName): void
    {
        $phone = PhoneNumber::parse('0' . $areaCode . ' 123 45 67');

        self::assertTrue($phone->isLandline());
        self::assertFalse($phone->isMobile());
        self::assertSame(PhoneType::LANDLINE, $phone->type());
        self::assertSame($areaName, $phone->areaName());
    }

    public function testGeographicAreaCodeDataMatchesExpectedBtkList(): void
    {
        $expected = [];

        foreach (self::supportedGeographicAreaCodes() as [$areaCode]) {
            $expected[] = $areaCode;
        }

        self::assertSame($expected, TurkishNumberingPlan::landlineAreaCodes());
    }

    public function testFormatsNumbers(): void
    {
        $phone = PhoneNumber::parse('0555 000 00 00');

        self::assertSame('+905550000000', $phone->e164());
        self::assertSame('0555 000 00 00', $phone->national());
        self::assertSame('+90 555 000 00 00', $phone->international());
        self::assertSame('5550000000', $phone->compact());
        self::assertSame('+905550000000', (string) $phone);
    }

    public function testFormatEnumAndString(): void
    {
        $phone = PhoneNumber::parse('0216 123 45 67');

        self::assertSame('+902161234567', $phone->format(Format::E164));
        self::assertSame('0216 123 45 67', $phone->format('national'));
        self::assertSame('+90 216 123 45 67', $phone->format('international'));
        self::assertSame('2161234567', $phone->format('compact'));
    }

    public function testFacadeApi(): void
    {
        self::assertTrue(TrPhone::validate('+90 532 123 45 67'));
        self::assertFalse(TrPhone::validate('+49 151 12345678'));
        self::assertSame('5321234567', TrPhone::normalize('+90 532 123 45 67'));
        self::assertSame('+905321234567', TrPhone::format('0532 123 45 67'));
        self::assertSame('0532 123 45 67', TrPhone::format('0532 123 45 67', Format::NATIONAL));
        self::assertInstanceOf(PhoneNumber::class, TrPhone::parse('0532 123 45 67'));
    }

    /**
     * @return iterable<string, array{string, class-string<InvalidPhoneNumberException>}>
     */
    public static function invalidInputs(): iterable
    {
        yield 'empty' => ['', InvalidPhoneNumberException::class];
        yield 'letters' => ['abc', InvalidPhoneNumberException::class];
        yield 'unsupported country' => ['+4915112345678', InvalidPhoneNumberException::class];
        yield 'international 00 non turkey' => ['004915112345678', InvalidPhoneNumberException::class];
        yield 'too short' => ['123', InvalidPhoneNumberException::class];
        yield 'too long national' => ['055500000009', InvalidPhoneNumberException::class];
        yield 'invalid prefix' => ['09993699678', InvalidPhoneNumberException::class];
        yield 'unallocated mobile prefix' => ['05803699678', InvalidPhoneNumberException::class];
        yield 'malformed plus' => ['0555+0000000', InvalidPhoneNumberException::class];
        yield 'bare country code' => ['905550000000', InvalidPhoneNumberException::class];
    }

    /**
     * @param class-string<InvalidPhoneNumberException> $exception
     */
    #[DataProvider('invalidInputs')]
    public function testRejectsInvalidInputs(string $input, string $exception): void
    {
        $this->expectException($exception);

        PhoneNumber::parse($input);
    }

    public function testTryParseDoesNotThrow(): void
    {
        self::assertNull(PhoneNumber::tryParse('abc'));
        self::assertNull(TrPhone::tryParse('+49 151 12345678'));
        self::assertInstanceOf(PhoneNumber::class, PhoneNumber::tryParse('0555 000 00 00'));
    }

    public function testPrefixAllocationIsNotCurrentCarrier(): void
    {
        self::assertSame('Turkcell', PhoneNumber::parse('0532 123 45 67')->prefixOperator());
        self::assertSame('Vodafone', PhoneNumber::parse('0542 123 45 67')->prefixAllocation());
        self::assertSame('Turk Telekom', PhoneNumber::parse('0505 123 45 67')->prefixOperator());
        self::assertSame('Machine-to-machine service', PhoneNumber::parse('0570 123 45 67')->prefixOperator());
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function specialMobileServicePrefixes(): iterable
    {
        yield '510 mobile virtual network service' => ['510', 'Mobile virtual network service', 'Mobile virtual network service numbers'];
        yield '512 call service' => ['512', 'Turk Telekom call service', 'Call service numbers'];
        yield '516 mobile virtual network service' => ['516', 'Mobile virtual network service', 'Mobile virtual network service numbers'];
        yield '561 mobile virtual network service' => ['561', 'Mobile virtual network service', 'Mobile virtual network service numbers'];
        yield '570 machine to machine' => ['570', 'Machine-to-machine service', 'Machine-to-machine (M2M) service numbers'];
        yield '571 machine to machine' => ['571', 'Machine-to-machine service', 'Machine-to-machine (M2M) service numbers'];
        yield '572 machine to machine' => ['572', 'Machine-to-machine service', 'Machine-to-machine (M2M) service numbers'];
        yield '573 machine to machine' => ['573', 'Machine-to-machine service', 'Machine-to-machine (M2M) service numbers'];
        yield '574 machine to machine' => ['574', 'Machine-to-machine service', 'Machine-to-machine (M2M) service numbers'];
        yield '575 machine to machine' => ['575', 'Machine-to-machine service', 'Machine-to-machine (M2M) service numbers'];
        yield '592 GMPCS' => ['592', 'Globalstar GMPCS', 'GMPCS mobile satellite service numbers'];
        yield '594 GSM-R' => ['594', 'TCDD GSM-R', 'GSM-R railway communication service numbers'];
    }

    #[DataProvider('specialMobileServicePrefixes')]
    public function testSpecialFiveXxServicesRemainValidMobileNumberingPlanEntries(
        string $prefix,
        string $allocation,
        string $service,
    ): void {
        $phone = PhoneNumber::parse('0' . $prefix . ' 123 45 67');

        self::assertTrue($phone->isMobile());
        self::assertSame(PhoneType::MOBILE, $phone->type());
        self::assertSame($allocation, $phone->prefixOperator());
        self::assertSame($service, $phone->prefixService());
    }
}

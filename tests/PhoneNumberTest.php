<?php

declare(strict_types=1);

namespace Kariha\TrPhone\Tests;

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
}

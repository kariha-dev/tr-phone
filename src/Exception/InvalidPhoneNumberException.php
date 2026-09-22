<?php

declare(strict_types=1);

namespace Kariha\TrPhone\Exception;

use InvalidArgumentException;

final class InvalidPhoneNumberException extends InvalidArgumentException
{
    public static function emptyInput(): self
    {
        return new self('Phone number input is empty.');
    }

    public static function invalidCharacters(): self
    {
        return new self('Phone number contains unsupported characters.');
    }

    public static function unsupportedCountryCode(): self
    {
        return new self('Only Turkish phone numbers with country code +90 are supported.');
    }

    public static function invalidLength(string $digits): self
    {
        return new self(sprintf('Expected a 10-digit Turkish national number, got %d digits.', strlen($digits)));
    }

    public static function invalidNationalPrefix(string $digits): self
    {
        return new self(sprintf('Unrecognized Turkish phone number prefix in "%s".', $digits));
    }
}

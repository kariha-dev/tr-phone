<?php

declare(strict_types=1);

namespace Kariha\TrPhone;

final class TrPhone
{
    public static function parse(string $input): PhoneNumber
    {
        return PhoneNumber::parse($input);
    }

    public static function tryParse(string $input): ?PhoneNumber
    {
        return PhoneNumber::tryParse($input);
    }

    public static function validate(string $input): bool
    {
        return PhoneNumber::isValidInput($input);
    }

    public static function normalize(string $input): string
    {
        return PhoneNumber::parse($input)->digits();
    }

    public static function format(string $input, Format|string $format = Format::E164): string
    {
        return PhoneNumber::parse($input)->format($format);
    }
}

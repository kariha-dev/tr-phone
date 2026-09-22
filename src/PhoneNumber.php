<?php

declare(strict_types=1);

namespace Kariha\TrPhone;

use Kariha\TrPhone\Data\TurkishNumberingPlan;
use Kariha\TrPhone\Exception\InvalidPhoneNumberException;

final class PhoneNumber
{
    private function __construct(
        private readonly string $digits,
        private readonly PhoneType $type,
    ) {
    }

    public static function parse(string $input): self
    {
        $digits = self::normalizeToDigits($input);
        $type = TurkishNumberingPlan::typeFor($digits);

        if ($type === null) {
            throw InvalidPhoneNumberException::invalidNationalPrefix($digits);
        }

        return new self($digits, $type);
    }

    public static function tryParse(string $input): ?self
    {
        try {
            return self::parse($input);
        } catch (InvalidPhoneNumberException) {
            return null;
        }
    }

    public static function isValidInput(string $input): bool
    {
        return self::tryParse($input) !== null;
    }

    public function isValid(): bool
    {
        return true;
    }

    public function isMobile(): bool
    {
        return $this->type === PhoneType::MOBILE;
    }

    public function isLandline(): bool
    {
        return $this->type === PhoneType::LANDLINE;
    }

    public function type(): PhoneType
    {
        return $this->type;
    }

    public function digits(): string
    {
        return $this->digits;
    }

    public function compact(): string
    {
        return $this->digits;
    }

    public function prefix(): string
    {
        return substr($this->digits, 0, 3);
    }

    public function prefixOperator(): ?string
    {
        return TurkishNumberingPlan::prefixAllocation($this->digits);
    }

    public function prefixAllocation(): ?string
    {
        return $this->prefixOperator();
    }

    public function areaName(): ?string
    {
        return TurkishNumberingPlan::areaName($this->digits);
    }

    public function e164(): string
    {
        return '+90' . $this->digits;
    }

    public function national(): string
    {
        return sprintf(
            '0%s %s %s %s',
            substr($this->digits, 0, 3),
            substr($this->digits, 3, 3),
            substr($this->digits, 6, 2),
            substr($this->digits, 8, 2),
        );
    }

    public function international(): string
    {
        return sprintf(
            '+90 %s %s %s %s',
            substr($this->digits, 0, 3),
            substr($this->digits, 3, 3),
            substr($this->digits, 6, 2),
            substr($this->digits, 8, 2),
        );
    }

    public function format(Format|string $format): string
    {
        $format = is_string($format) ? Format::from(strtolower($format)) : $format;

        return match ($format) {
            Format::E164 => $this->e164(),
            Format::NATIONAL => $this->national(),
            Format::INTERNATIONAL => $this->international(),
            Format::COMPACT => $this->compact(),
        };
    }

    public function __toString(): string
    {
        return $this->e164();
    }

    private static function normalizeToDigits(string $input): string
    {
        $value = trim($input);

        if ($value === '') {
            throw InvalidPhoneNumberException::emptyInput();
        }

        if (!preg_match('/^\+?[0-9\s().-]+$/u', $value)) {
            throw InvalidPhoneNumberException::invalidCharacters();
        }

        if (substr_count($value, '+') > 1 || (str_contains($value, '+') && !str_starts_with($value, '+'))) {
            throw InvalidPhoneNumberException::invalidCharacters();
        }

        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === null || $digits === '') {
            throw InvalidPhoneNumberException::emptyInput();
        }

        if (str_starts_with($value, '+')) {
            if (!str_starts_with($digits, '90')) {
                throw InvalidPhoneNumberException::unsupportedCountryCode();
            }

            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0090')) {
            $digits = substr($digits, 4);
        } elseif (str_starts_with($digits, '00')) {
            throw InvalidPhoneNumberException::unsupportedCountryCode();
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== 10) {
            throw InvalidPhoneNumberException::invalidLength($digits);
        }

        return $digits;
    }
}

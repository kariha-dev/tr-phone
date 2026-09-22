<?php

declare(strict_types=1);

namespace Kariha\TrPhone\Data;

use Kariha\TrPhone\PhoneType;

final class TurkishNumberingPlan
{
    public const SOURCE = 'BTK Ulusal Numaralandirma Plani / Genel Numaralandirma Plani, verified 2026-09-23.';
    public const SOURCE_URL = 'https://www.btk.gov.tr/genel-numaralandirma-plani';

    /**
     * @var array<int, string>
     */
    private const LANDLINE_AREAS = [
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

    /**
     * Prefix allocation data, not current carrier data.
     *
     * @var array<int, string>
     */
    private const MOBILE_PREFIX_ALLOCATIONS = [
        '501' => 'Turk Telekom',
        '505' => 'Turk Telekom',
        '506' => 'Turk Telekom',
        '507' => 'Turk Telekom',
        '510' => 'Mobile virtual network service',
        '512' => 'Turk Telekom call service',
        '516' => 'Mobile virtual network service',
        '530' => 'Turkcell',
        '531' => 'Turkcell',
        '532' => 'Turkcell',
        '533' => 'Turkcell',
        '534' => 'Turkcell',
        '535' => 'Turkcell',
        '536' => 'Turkcell',
        '537' => 'Turkcell',
        '538' => 'Turkcell',
        '539' => 'Turkcell',
        '540' => 'Vodafone',
        '541' => 'Vodafone',
        '542' => 'Vodafone',
        '543' => 'Vodafone',
        '544' => 'Vodafone',
        '545' => 'Vodafone',
        '546' => 'Vodafone',
        '547' => 'Vodafone',
        '548' => 'Vodafone',
        '549' => 'Vodafone',
        '551' => 'Turk Telekom',
        '552' => 'Turk Telekom',
        '553' => 'Turk Telekom',
        '554' => 'Turk Telekom',
        '555' => 'Turk Telekom',
        '559' => 'Turk Telekom',
        '561' => 'Mobile virtual network service',
        '570' => 'Machine-to-machine service',
        '571' => 'Machine-to-machine service',
        '572' => 'Machine-to-machine service',
        '573' => 'Machine-to-machine service',
        '574' => 'Machine-to-machine service',
        '575' => 'Machine-to-machine service',
        '592' => 'Globalstar GMPCS',
        '594' => 'TCDD GSM-R',
    ];

    public static function typeFor(string $digits): ?PhoneType
    {
        if (isset(self::MOBILE_PREFIX_ALLOCATIONS[self::prefix($digits)])) {
            return PhoneType::MOBILE;
        }

        if (isset(self::LANDLINE_AREAS[self::prefix($digits)])) {
            return PhoneType::LANDLINE;
        }

        return null;
    }

    public static function prefixAllocation(string $digits): ?string
    {
        return self::MOBILE_PREFIX_ALLOCATIONS[self::prefix($digits)] ?? null;
    }

    public static function areaName(string $digits): ?string
    {
        return self::LANDLINE_AREAS[self::prefix($digits)] ?? null;
    }

    public static function isRecognized(string $digits): bool
    {
        return self::typeFor($digits) !== null;
    }

    /**
     * @return list<string>
     */
    public static function mobilePrefixes(): array
    {
        return array_map('strval', array_keys(self::MOBILE_PREFIX_ALLOCATIONS));
    }

    /**
     * @return list<string>
     */
    public static function landlineAreaCodes(): array
    {
        return array_map('strval', array_keys(self::LANDLINE_AREAS));
    }

    private static function prefix(string $digits): string
    {
        return substr($digits, 0, 3);
    }
}

<?php

declare(strict_types=1);

namespace Kariha\TrPhone;

enum Format: string
{
    case E164 = 'e164';
    case NATIONAL = 'national';
    case INTERNATIONAL = 'international';
    case COMPACT = 'compact';
}

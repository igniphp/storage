<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Utils\Enum;

class HydrationMode extends Enum
{
    public const BY_REFERENCE = 'by_reference';
    public const BY_VALUE = 'by_value';
}

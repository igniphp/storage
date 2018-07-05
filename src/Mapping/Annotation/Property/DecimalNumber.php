<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 */
class DecimalNumber extends Property
{
    public $scale;

    public $precision;

    public function getType(): string
    {
        return 'decimal';
    }
}

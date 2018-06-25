<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Types;

use Igni\Storage\Mapping\Annotation\Type;

/**
 * @Annotation
 */
class DecimalNumber extends Type
{
    public $scale;

    public $precision;

    public function getType(): string
    {
        return 'decimal';
    }
}

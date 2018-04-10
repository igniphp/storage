<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Igni\Storage\Mapping\Annotations\Type;

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

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Types;

use Igni\Storage\Mapping\Annotation\Type;

/**
 * @Annotation
 */
class IntegerNumber extends Type
{
    public function getType(): string
    {
        return 'integer';
    }
}

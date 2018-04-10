<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Igni\Storage\Mapping\Annotations\Type;

/**
 * @Annotation
 */
class Enum extends Type
{
    public $values;

    public function getType(): string
    {
        return 'enum';
    }
}

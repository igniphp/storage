<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Types;

use Igni\Storage\Mapping\Annotation\Type;

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

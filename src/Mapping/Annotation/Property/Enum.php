<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 */
class Enum extends Property
{
    public $values;

    public function getType(): string
    {
        return 'enum';
    }

    public function getValues(): array
    {
        return $this->values ?? (array) $this->value;
    }
}

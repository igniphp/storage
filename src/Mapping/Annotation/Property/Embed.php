<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 */
class Embed extends Property
{
    public $class;
    public $storeAs;

    public function getType(): string
    {
        return 'embed';
    }

    public function getClass(): string
    {
        return $this->class ?? $this->value;
    }
}

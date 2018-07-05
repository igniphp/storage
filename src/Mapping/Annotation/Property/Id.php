<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Id\Uuid;
use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 */
class Id extends Property
{
    public $class;

    public function getType(): string
    {
        return 'id';
    }

    public function getClass(): string
    {
        return $this->class ?? $this->value ?? Uuid::class;
    }
}

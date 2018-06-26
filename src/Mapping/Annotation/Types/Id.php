<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Types;

use Igni\Storage\Id\Uuid;
use Igni\Storage\Mapping\Annotation\Type;

/**
 * @Annotation
 */
class Id extends Type
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

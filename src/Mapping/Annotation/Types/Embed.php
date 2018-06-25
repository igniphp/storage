<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Types;

use Igni\Storage\Mapping\Annotation\Type;

/**
 * @Annotation
 */
class Embed extends Type
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

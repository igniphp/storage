<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Igni\Storage\Mapping\Annotations\Type;

/**
 * @Annotation
 */
class Reference extends Type
{
    public $target;

    public function getTarget(): string
    {
        return $this->target ?? $this->value;
    }

    public function getType(): string
    {
        return 'reference';
    }
}

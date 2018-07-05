<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 */
class Reference extends Property
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

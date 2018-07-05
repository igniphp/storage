<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 */
class Text extends Property
{
    public function getType(): string
    {
        return 'string';
    }
}

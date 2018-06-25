<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Types;

use Igni\Storage\Mapping\Annotation\Type;

/**
 * @Annotation
 * @see \Igni\Storage\Mapping\Strategy\Date
 */
class Date extends Type
{
    public $timezone = 'UTC';

    public $format = 'Ymd';

    public $immutable = false;

    public function getType(): string
    {
        return 'date';
    }
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Igni\Storage\Mapping\Annotations\Type;

/**
 * @Annotation
 */
class Date extends Type
{
    public $timezone = 'UTC';

    public $format = 'Ymd';
}

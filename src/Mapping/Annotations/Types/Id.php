<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Igni\Storage\Uuid;
use Igni\Storage\Mapping\Annotations\Type;

/**
 * @Annotation
 */
class Id extends Type
{
    public $strategy = Uuid::class;
}

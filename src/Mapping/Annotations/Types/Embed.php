<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Embed extends Annotation
{
    public $class;
}

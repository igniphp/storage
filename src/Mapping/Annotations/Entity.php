<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Entity extends Annotation
{
    public $source;

    public $hydrator;
}

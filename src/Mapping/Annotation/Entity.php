<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Entity extends Annotation
{
    public $source;
    public $connection = 'default';
    public $hydrator;
}

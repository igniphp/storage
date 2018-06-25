<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class EmbeddedEntity extends Annotation
{
    public $storeAs = 'json';
    public $hydrator;
}

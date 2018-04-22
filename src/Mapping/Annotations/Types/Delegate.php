<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Doctrine\Common\Annotations\Annotation;
use Igni\Storage\Mapping\Annotations\Type;

/**
 * @Annotation
 */
class Delegate extends Type
{
    public $hydrator;

    public $extractor;

    public function getType(): string
    {
        return 'delegate';
    }
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations\Types;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Delegate extends Annotation
{
    public $hydrator;
    public $extractor;

    public function getType(): string
    {
        return 'delegate';
    }
}

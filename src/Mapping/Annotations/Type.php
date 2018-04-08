<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Type extends Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type = 'text';

    /**
     * @var mixed[]
     */
    public $options = [];

    /**
     * @var string
     */
    public $strategy;
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function getAttributes(): array
    {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $value) {
            $method = 'get' . ucfirst($name);
            if (method_exists($this, $method)) {
                $attributes[$name] = $this->$method();
            }
        }
        unset($attributes['type'], $attributes['value'], $attributes['name']);

        return $attributes;
    }
}

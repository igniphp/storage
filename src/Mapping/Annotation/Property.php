<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;
use function get_object_vars;
use function method_exists;
use function ucfirst;

/**
 * @Annotation
 */
class Property extends Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type = 'text';

    public $class;

    public $readonly = false;

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
        //unset($attributes['type'], $attributes['value'], $attributes['name']);

        return $attributes;
    }
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData;

use Igni\Storage\Mapping\Strategy\Text;
use Closure;

final class PropertyMetaData
{
    private $attributes = [];
    private $propertyName;
    private $fieldName;
    private $type;
    private $accessor;
    private $writer;

    public function __construct(string $propertyName, string $type = Text::class)
    {
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->accessor = function () use ($propertyName) {
            return $this->$propertyName;
        };
        $this->writer = function ($value) use ($propertyName)  {
            $this->$propertyName = $value;
        };
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->propertyName;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setFieldName(string $name): void
    {
        $this->fieldName = $name;
    }

    public function getFieldName(): string
    {
        return $this->fieldName ?? $this->propertyName;
    }

    public function setValue($object, $value): void
    {
        Closure::bind($this->writer, $object, $object)($value);
    }

    public function getValue($object)
    {
        return Closure::bind($this->accessor, $object, $object)();
    }

    public function __sleep()
    {
        return [
            'attributes',
            'propertyName',
            'fieldName',
            'type',
        ];
    }

    public function __wakeup()
    {
        $propertyName = $this->propertyName;

        $this->accessor = function () use ($propertyName) {
            return $this->$propertyName;
        };
        $this->writer = function ($value) use ($propertyName)  {
            $this->$propertyName = $value;
        };
    }
}

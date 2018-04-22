<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData;

use Igni\Storage\Mapping\Strategy\Text;
use ReflectionProperty;

final class PropertyMetaData
{
    private $attributes = [];
    private $propertyName;
    private $fieldName;
    private $type;
    private $entityClass;
    private $propertyReflection;

    public function __construct(string $entityClass, string $propertyName, string $type = Text::class)
    {
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->entityClass = $entityClass;
        $this->propertyReflection = new ReflectionProperty($entityClass, $propertyName);
        $this->propertyReflection->setAccessible(true);
    }

    public function getClass(): string
    {
        return $this->entityClass;
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
        $this->propertyReflection->setValue($object, $value);
    }

    public function getValue($object)
    {
        return $this->propertyReflection->getValue($object);
    }

    public function __sleep()
    {
        return [
            'attributes',
            'propertyName',
            'fieldName',
            'type',
            'entityClass',
        ];
    }

    public function __wakeup()
    {
        $this->propertyReflection = new ReflectionProperty($this->entityClass, $this->propertyName);
    }
}

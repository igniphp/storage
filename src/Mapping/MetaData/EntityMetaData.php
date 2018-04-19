<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\Strategy\Id;
use ReflectionClass;

final class EntityMetaData
{
    private $class;
    private $hydratorClassName;
    private $properties;
    private $reflectionClass;
    private $storage;
    private $embed = true;
    private $parentHydrator;

    public function __construct(string $class, array $properties)
    {
        $this->class = $class;
        $this->reflectionClass = new ReflectionClass($class);
        $this->hydratorClassName = str_replace('\\', '', $class) . 'Hydrator';

        foreach ($properties as $name => $attributes) {
            if (!isset($attributes['field'])) {
                $attributes['field'] = $name;
            }
            $this->addProperty($name, $attributes);
        }
    }

    public function makeEmbed(): void
    {
        $this->storage = null;
        $this->embed = true;
    }

    public function isEmbed(): bool
    {
        return $this->embed;
    }

    public function isStorable(): bool
    {
        return $this->storage !== null;
    }

    public function setStorage(string $storage): void
    {
        $this->storage = $storage;
        $this->embed = false;
    }

    public function getStorage(): string
    {
        return $this->storage;
    }

    public function setParentHydratorClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new MappingException("Cannot set parent hydrator, class (${className}) does not exists.");
        }
        $this->parentHydrator = $className;
    }

    public function hasParentHydratorClass(): bool
    {
        return $this->parentHydrator !== null;
    }

    public function getParentHydratorClass(): string
    {
        return $this->parentHydrator;
    }

    /**
     * @param string $name
     * @param array $attributes<int, array {
     *     field: string,
     *     type: string,
     *     attributes: array
     * }>
     */
    protected function addProperty(string $name, array $attributes)
    {
        $this->properties[$name] = $attributes;
    }

    public function getIdentifierName(): string
    {
        foreach ($this->properties as $name => $strategy) {
            if ($strategy === Id::class) {
                return $name;
            }
        }
    }

    public function getHydratorClassName(): string
    {
        return $this->hydratorClassName;
    }

    /**
     * @return array $attributes<int, array {
     *     field: string,
     *     type: string,
     *     attributes: array
     * }>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function createInstance(...$arguments)
    {
        if ($arguments) {
            return $this->reflectionClass->newInstanceArgs($arguments);
        }

        return $this->reflectionClass->newInstanceWithoutConstructor();
    }

    public function __sleep()
    {
        return [
            'class',
            'hydratorClassName',
            'namingStrategy',
            'properties',
            'storage',
            'parentHydrator',
        ];
    }

    public function __wakeup()
    {
        $this->reflectionClass = new ReflectionClass($this->class);
    }
}

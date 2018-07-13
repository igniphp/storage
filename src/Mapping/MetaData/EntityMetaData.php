<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\Strategy\Id;
use ReflectionClass;

/**
 * Keeps information how entity should be mapped.
 *
 * @package Igni\Storage\Mapping\MetaData
 */
final class EntityMetaData
{
    /**
     * Entity class (FQCN)
     * @var string
     */
    private $class;

    /**
     * Generated hydrator class name (not FQCN)
     * @var string string
     */
    private $hydratorClassName;

    /**
     * Entity's mapped properties metadata.
     * @var PropertyMetaData[]
     */
    private $properties = [];

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * Either name of table or connection or any other namespace
     * where entity is stored.
     * @var string
     */
    private $source;

    /**
     * Connection that will be used when no repository is registered
     * for the entity.
     * @var string
     */
    private $connection = 'default';

    /**
     * Provides information if entity is embed entity.
     * @var bool
     */
    private $embed = true;

    /**
     * Keeps user defined hydrator's class (FQCN).
     * @var string
     */
    private $customHydrator;

    /**
     * Keeps mapped properties to storage fields.
     * @var string[]
     */
    private $fields = [];

    /**
     * Contains identifier property.
     * @var PropertyMetaData|null
     */
    private $identifier;

    /**
     * @param string $class
     *
     * @throws \ReflectionException
     */
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->reflectionClass = new ReflectionClass($class);
        $this->hydratorClassName = '_' . str_replace('\\', '', $class) . 'Hydrator';
    }

    public function makeEmbed(): void
    {
        $this->source = null;
        $this->embed = true;
    }

    public function isEmbed(): bool
    {
        return $this->embed;
    }

    public function isStorable(): bool
    {
        return $this->source !== null && $this->hasIdentifier();
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
        $this->embed = false;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setConnection(string $name = 'default'): void
    {
        $this->connection = $name;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function setCustomHydratorClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new MappingException("Cannot set parent hydrator, class (${className}) does not exists.");
        }
        $this->customHydrator = $className;
    }

    public function definesCustomHydrator(): bool
    {
        return $this->customHydrator !== null;
    }

    public function getCustomHydratorClass(): string
    {
        return $this->customHydrator;
    }

    public function getProperty(string $name): PropertyMetaData
    {
        if (!isset($this->properties[$name])) {
            throw new MappingException("Property ${name} is undefined.");
        }
        return $this->properties[$name];
    }

    public function addProperty(PropertyMetaData $property): void
    {
        $this->properties[$property->getName()] = $property;
        if ($property->getType() === Id::class) {
            $this->identifier = $property;
        }
    }

    public function hasIdentifier(): bool
    {
        return $this->identifier !== null;
    }

    public function getIdentifier(): PropertyMetaData
    {
        if (!$this->hasIdentifier()) {
            throw new MappingException("Entity {$this->class} defines no identifier.");
        }

        return $this->identifier;
    }

    public function getHydratorClassName(): string
    {
        return $this->hydratorClassName;
    }

    public function definesProperties(): bool
    {
        return !empty($this->properties);
    }

    /**
     * @return PropertyMetaData[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getClass(): string
    {
        return $this->class;
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
            'properties',
            'source',
            'connection',
            'customHydrator',
        ];
    }

    public function __wakeup()
    {
        $this->reflectionClass = new ReflectionClass($this->class);

        foreach ($this->properties as $property) {
            $this->fields[] = $property->getFieldName();
            if ($property->getType() === Id::class) {
                $this->identifier = $property;
            }
        }
    }
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Exception\SchemaException;
use Igni\Storage\Mapping\Strategy\Id;
use IteratorAggregate;
use ArrayAccess;
use Iterator;
use ArrayIterator;

abstract class Schema implements IteratorAggregate, ArrayAccess
{
    private static $schemas = [];
    private static $loaded = [];
    private $namingStrategy;
    private $properties;
    private $id;

    private function __construct()
    {
        self::$loaded[static::class] = $this;
        self::$schemas[$this->getEntity()] = $this;
        $this->namingStrategy = new NamingStrategy\DefaultNamingStrategy();
    }

    protected function setNamingStrategy(NamingStrategy $namingStrategy): void
    {
        $this->namingStrategy = $namingStrategy;
    }

    protected function map(string $name, MappingStrategy $mappingStrategy)
    {
        $this->properties[$name] = $mappingStrategy;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->properties);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->properties[$offset]);
    }

    public function offsetGet($offset): MappingStrategy
    {
        return $this->properties[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw SchemaException::forSchemaMutation();
    }

    public function offsetUnset($offset)
    {
        throw SchemaException::forSchemaMutation();
    }

    /**
     * @return MappingStrategy[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    abstract protected function define(): void;

    abstract public function getEntity(): string;

    public function getSource(): string
    {
        throw SchemaException::forUndefinedSource();
    }

    public function getId(): string
    {
        if ($this->id) {
            return $this->id;
        }

        foreach ($this as $property => $strategy) {
            if ($strategy instanceof Id) {
                return $this->id = $this->namingStrategy->map($property);
            }
        }
    }

    public function getNamingStrategy(): NamingStrategy
    {
        return $this->namingStrategy;
    }

    final public static function instance(): Schema
    {
        if (isset(self::$loaded[static::class])) {
            return self::$loaded[static::class];
        }

        $instance = new static();
        $instance->define();

        if (empty($instance->properties)) {
            throw SchemaException::forEmptySchemaDefinition();
        }

        self::$loaded[static::class] = $instance;
        self::$schemas[$instance->getEntity()] = $instance;

        return $instance;
    }

    public static function get(string $entity): Schema
    {
        if (isset(self::$schemas[$entity])) {
            return self::$schemas[$entity];
        }

        throw MappingException::forNonRegisteredSchema($entity);
    }
}

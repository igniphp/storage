<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Uuid;

final class Id implements MappingStrategy
{
    /**
     * @var Id|string
     */
    private $class;

    public function __construct(string $class = Uuid::class)
    {
        $this->class = $class;
    }

    public function hydrate($value)
    {
        $class = $this->class;
        return new $class($value);
    }

    public function extract($value)
    {
        return $value->getValue();
    }
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Mapping\MappingStrategy;

final class Text implements MappingStrategy
{
    private $length;

    public function __construct(int $length = null)
    {
        $this->length = $length;
    }

    public function hydrate($value): string
    {
        return (string) $value;
    }

    public function extract($value): string
    {
        return (string) $value;
    }

    public function hasLength(): bool
    {
        return $this->length !== null;
    }

    public function length(): int
    {
        if (!$this->hasLength()) {
            throw HydratorException::forPropertyMissArgument($this, 'length');
        }

        return $this->length;
    }
}

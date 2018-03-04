<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class DefinedMapping implements MappingStrategy
{
    private $hydrator;
    private $extractor;

    public function __construct(callable $hydrator, callable $extractor = null)
    {
        $this->hydrator = $hydrator;
        $this->extractor = $extractor;
    }

    public function hydrate($value, $entityManager = null)
    {
        return call_user_func($this->hydrator, $value, $entityManager);
    }

    public function hasExtractor(): bool
    {
        return $this->extractor !== null;
    }

    public function extract($value)
    {
        return call_user_func($this->extractor, $value);
    }
}

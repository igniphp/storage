<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\NamingStrategy;

use Igni\Storage\Mapping\NamingStrategy;

class DirectNaming implements NamingStrategy
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function map(string $name): string
    {
        return $this->map[$name] ?? $name;
    }
}

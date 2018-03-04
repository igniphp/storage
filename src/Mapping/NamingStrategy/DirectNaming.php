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

    public function addRule(string $from, $to): void
    {
        $this->map[$from] = $to;
    }

    public function hasRule(string $from): bool
    {
        return isset($this->map[$from]);
    }

    public function removeRule(string $from): void
    {
        if ($this->hasRule($from)) {
            unset($this->map[$from]);
        }
    }

    public function map(string $name): string
    {
        return $this->map[$name] ?? $name;
    }
}

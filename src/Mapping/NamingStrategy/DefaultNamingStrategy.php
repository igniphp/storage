<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\NamingStrategy;

use Igni\Storage\Mapping\NamingStrategy;

class DefaultNamingStrategy implements NamingStrategy
{
    public function map(string $name): string
    {
        return $name;
    }

    public function addRule(string $from, $to): void
    {
        // Intentionally left empty.
    }

    public function hasRule(string $from): bool
    {
        return true;
    }

    public function removeRule(string $from): void
    {
        // Intentionally left empty.
    }
}

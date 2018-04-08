<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\NamingStrategy;

use Igni\Storage\Mapping\NamingStrategy;

class DefaultNamingStrategy implements NamingStrategy
{
    public function map(string $name): string
    {
        return $name;
    }
}

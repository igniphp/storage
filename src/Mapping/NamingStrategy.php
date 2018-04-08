<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

interface NamingStrategy
{
    public function map(string $from): string;
}

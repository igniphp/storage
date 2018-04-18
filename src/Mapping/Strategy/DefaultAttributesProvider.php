<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

interface DefaultAttributesProvider
{
    public static function getDefaultAttributes(): array;
}

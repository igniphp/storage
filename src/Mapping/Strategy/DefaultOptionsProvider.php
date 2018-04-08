<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

interface DefaultOptionsProvider
{
    public static function getDefaultOptions(): array;
}

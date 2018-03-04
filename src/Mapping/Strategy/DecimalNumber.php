<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class DecimalNumber implements MappingStrategy
{
    private const DEFAULT_SCALE = 10;
    private const DEFAULT_PRECISION = 2;

    private $scale;
    private $precision;

    public function __construct(int $scale = self::DEFAULT_SCALE, int $precision = self::DEFAULT_PRECISION)
    {
        $this->scale = $scale;
        $this->precision = $precision;
    }

    public function hydrate($value): string
    {
        return (string) $value;
    }

    public function extract($value): string
    {
        return (string) $value;
    }
}

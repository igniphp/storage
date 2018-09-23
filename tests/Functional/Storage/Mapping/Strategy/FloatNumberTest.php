<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\FloatNumber;
use PHPUnit\Framework\TestCase;

final class FloatNumberTest extends TestCase
{
    public function testExtract(): void
    {
        $value = 1;
        $attributes = [];
        FloatNumber::extract($value, $attributes);

        self::assertSame(1.0, $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = [];
        FloatNumber::extract($value, $attributes);

        self::assertSame(0.0, $value);
    }

    public function testHydrate(): void
    {
        $value = 1;
        $attributes = [];
        FloatNumber::hydrate($value, $attributes);

        self::assertSame(1.0, $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = [];
        FloatNumber::hydrate($value, $attributes);

        self::assertSame(0.0, $value);
    }
}

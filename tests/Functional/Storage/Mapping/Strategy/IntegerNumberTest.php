<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\IntegerNumber;
use PHPUnit\Framework\TestCase;

final class IntegerNumberTest extends TestCase
{
    public function testExtract(): void
    {
        $value = '1';
        $attributes = [];
        IntegerNumber::extract($value, $attributes);

        self::assertSame(1, $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = [];
        IntegerNumber::extract($value, $attributes);

        self::assertSame(0, $value);
    }

    public function testHydrate(): void
    {
        $value = 1.0;
        $attributes = [];
        IntegerNumber::hydrate($value, $attributes);

        self::assertSame(1, $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = [];
        IntegerNumber::hydrate($value, $attributes);

        self::assertSame(0, $value);
    }
}

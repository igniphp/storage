<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\IntegerNumber;
use Igni\Utils\TestCase;

final class IntegerNumberTest extends TestCase
{
    public function testExtract(): void
    {
        $value = '1';
        $attributes = [];
        eval(IntegerNumber::getExtractor());

        self::assertSame(1, $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = [];
        eval(IntegerNumber::getExtractor());

        self::assertSame(0, $value);
    }

    public function testHydrate(): void
    {
        $value = 1.0;
        $attributes = [];
        eval(IntegerNumber::getHydrator());

        self::assertSame(1, $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = [];
        eval(IntegerNumber::getHydrator());

        self::assertSame(0, $value);
    }
}

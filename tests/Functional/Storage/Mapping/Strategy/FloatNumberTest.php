<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\FloatNumber;
use Igni\Utils\TestCase;

final class FloatNumberTest extends TestCase
{
    public function testExtract(): void
    {
        $value = 1;
        $attributes = [];
        eval(FloatNumber::getExtractor());

        self::assertSame(1.0, $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = [];
        eval(FloatNumber::getExtractor());

        self::assertSame(0.0, $value);
    }

    public function testHydrate(): void
    {
        $value = 1;
        $attributes = [];
        eval(FloatNumber::getHydrator());

        self::assertSame(1.0, $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = [];
        eval(FloatNumber::getHydrator());

        self::assertSame(0.0, $value);
    }
}

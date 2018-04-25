<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\Text;
use Igni\Utils\TestCase;

final class TextTest extends TestCase
{
    public function testExtract(): void
    {
        $value = 1;
        $attributes = [];
        eval(Text::getExtractor());

        self::assertSame('1', $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = [];
        eval(Text::getExtractor());

        self::assertSame('', $value);
    }

    public function testHydrate(): void
    {
        $value = 1;
        $attributes = [];
        eval(Text::getHydrator());

        self::assertSame('1', $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = [];
        eval(Text::getHydrator());

        self::assertSame('', $value);
    }
}

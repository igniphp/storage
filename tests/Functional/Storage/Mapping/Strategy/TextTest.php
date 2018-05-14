<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\Text;
use PHPUnit\Framework\TestCase;

final class TextTest extends TestCase
{
    public function testExtract(): void
    {
        $value = 1;
        $attributes = [];
        Text::extract($value, $attributes);

        self::assertSame('1', $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = [];
        Text::extract($value, $attributes);

        self::assertSame('', $value);
    }

    public function testHydrate(): void
    {
        $value = 1;
        $attributes = [];
        Text::hydrate($value, $attributes);

        self::assertSame('1', $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = [];
        Text::hydrate($value, $attributes);

        self::assertSame('', $value);
    }
}

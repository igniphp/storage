<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\DecimalNumber;
use Igni\Utils\TestCase;

final class DecimalNumberTest extends TestCase
{
    public function testExtract(): void
    {
        $value = '10.00';
        $attributes = DecimalNumber::getDefaultAttributes();
        eval(DecimalNumber::getExtractor());

        self::assertSame('10.00', $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = DecimalNumber::getDefaultAttributes();
        eval(DecimalNumber::getExtractor());

        self::assertSame('0.00', $value);
    }

    public function testHydrate(): void
    {
        $decimal = '10.000';
        $value = $decimal;
        $attributes = DecimalNumber::getDefaultAttributes();
        eval(DecimalNumber::getHydrator());

        self::assertEquals('10.00', $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = DecimalNumber::getDefaultAttributes();
        eval(DecimalNumber::getHydrator());

        self::assertSame('0.00', $value);
    }

    public function testOverflowScale(): void
    {
        $value = '100.001';
        $attributes = [
            'scale' => 2,
            'precision' => 2,
        ];
        eval(DecimalNumber::getHydrator());

        self::assertSame('99.00', $value);
    }
}

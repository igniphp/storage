<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\Strategy\DecimalNumber;
use PHPUnit\Framework\TestCase;

final class DecimalNumberTest extends TestCase
{
    public function testExtract(): void
    {
        $value = '10.00';
        $attributes = DecimalNumber::getDefaultAttributes();
        DecimalNumber::extract($value, $attributes);

        self::assertSame('10.00', $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = DecimalNumber::getDefaultAttributes();
        DecimalNumber::extract($value, $attributes);

        self::assertSame('0.00', $value);
    }

    public function testHydrate(): void
    {
        $decimal = '10.000';
        $value = $decimal;
        $attributes = DecimalNumber::getDefaultAttributes();
        DecimalNumber::hydrate($value, $attributes);

        self::assertEquals('10.00', $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = DecimalNumber::getDefaultAttributes();
        DecimalNumber::hydrate($value, $attributes);

        self::assertSame('0.00', $value);
    }

    public function testOverflowPrecision(): void
    {
        $value = '100.001';
        $attributes = [
            'scale' => 2,
            'precision' => 4,
        ];
        DecimalNumber::hydrate($value, $attributes);

        self::assertSame('99.00', $value);
    }

    public function testScaleExcessPrecision(): void
    {
        $this->expectException(MappingException::class);
        $value = '1';
        $attributes = [
            'scale' => 9,
            'precision' => 4,
        ];
        DecimalNumber::hydrate($value, $attributes);
    }
}

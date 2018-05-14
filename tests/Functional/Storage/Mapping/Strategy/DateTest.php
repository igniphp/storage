<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\Strategy\Date;
use PHPUnit\Framework\TestCase;

final class DateTest extends TestCase
{
    public function testExtract(): void
    {
        $value = new \DateTime('2017-01-01');
        $attributes = [
            'format' => 'd-Y-m',
        ] + Date::getDefaultAttributes();
        Date::extract($value, $attributes);

        self::assertSame('01-2017-01', $value);
    }

    public function testExtractNull(): void
    {
        $value = null;
        $attributes = Date::getDefaultAttributes();
        Date::extract($value, $attributes);

        self::assertNull($value);
    }

    public function testHydrate(): void
    {
        $date = '2017-01-01';
        $value = $date;
        $attributes = Date::getDefaultAttributes();
        Date::hydrate($value, $attributes);

        self::assertEquals(new \DateTime($date), $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;
        $attributes = Date::getDefaultAttributes();
        Date::hydrate($value, $attributes);

        self::assertNull($value);
    }
}

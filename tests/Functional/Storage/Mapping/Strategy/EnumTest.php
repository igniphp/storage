<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\Strategy\Enum;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    public function testExtractFail(): void
    {
        $this->expectException(MappingException::class);
        Enum::extract($value, ['values' => 1]);
    }

    public function testHydrationFailOnNonExistentClass(): void
    {
        $this->expectException(MappingException::class);
        $value = 'a';
        Enum::hydrate($value, ['values' => 'SomeUnkonwnClass']);
    }

    public function testHydrationFailOnEmptyArray(): void
    {
        $this->expectException(MappingException::class);
        $value = 'a';
        Enum::hydrate($value, ['values' => []]);
    }

    /**
     * @param array $attributes
     * @param $value
     * @param $expected
     * @dataProvider provideValidDataForExtraction
     */
    public function testExtract(array $attributes, $value, $expected): void
    {
        Enum::extract($value, $attributes);
        self::assertSame($expected, $value);
    }

    /**
     * @param array $attributes
     * @param $value
     * @param $expected
     * @dataProvider provideValidDataForHydration
     */
    public function testHydrate(array $attributes, $value, $expected): void
    {
        Enum::hydrate($value, $attributes);
        self::assertEquals($expected, $value);
    }

    public function provideValidDataForHydration(): array
    {
        $list = ['values' => ['a', 'b', 'c']];
        $class = ['values' => AbcEnum::class];

        return [
            [
                [],
                null,
                null
            ],
            [
                $list,
                0,
                'a',
            ],
            [
                $class,
                0,
                new AbcEnum(0),
            ],
            [
                $list,
                2,
                'c',
            ],
            [
                $class,
                2,
                new AbcEnum(2),
            ],
        ];
    }

    public function provideValidDataForExtraction(): array
    {
        $list = ['values' => ['a', 'b', 'c']];
        $class = ['values' => AbcEnum::class];

        return [
            [
                $list,
                'a',
                0,
            ],
            [
                $class,
                new AbcEnum(0),
                'a',
            ],
            [
                $list,
                'c',
                2,
            ],
            [
                $class,
                new AbcEnum(2),
                'c',
            ],
        ];
    }
}

class AbcEnum implements \Igni\Storage\Enum
{
    private const VALUES = ['a', 'b', 'c'];
    private $value;

    public function __construct($value)
    {
        $this->value = self::VALUES[$value];
    }

    public function getValue()
    {
        return $this->value;
    }
}

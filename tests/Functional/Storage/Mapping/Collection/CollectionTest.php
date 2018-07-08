<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Collection;

use Igni\Storage\Mapping\Collection\Collection;
use ArrayIterator;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(Collection::class, new Collection());
        self::assertInstanceOf(Collection::class, new Collection(new ArrayIterator([1, 2, 3])));
    }

    public function testReduce(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5]));
        self::assertSame(
            15,
            $collection->reduce(
                function($value, $element) {
                    return $value + $element;
                },
                0
            )
        );
    }

    public function testMap(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5]));
        $mapped = $collection->map(function($el) { return $el + 1; });

        self::assertNotSame($mapped, $collection);
        self::assertSame([1, 2, 3, 4, 5], $collection->toArray());
        self::assertSame([2, 3, 4, 5, 6], $mapped->toArray());
    }

    public function testSort(): void
    {
        $collection = new Collection(new ArrayIterator([2, 4, 5, 1, 3]));
        $sorted = $collection->sort(function ($a, $b) { return $a <=> $b; });

        self::assertNotSame($sorted, $collection);
        self::assertSame([2, 4, 5, 1, 3], $collection->toArray());
        self::assertSame([1, 2, 3, 4, 5], $sorted->toArray());
    }

    public function testInsert(): void
    {
        $collection = new Collection(new ArrayIterator([1, 3, 4, 5]));
        $inserted = $collection->insert(1, 2);

        self::assertNotSame($inserted, $collection);
        self::assertSame([1, 3, 4, 5], $collection->toArray());
        self::assertSame([1, 2, 3, 4, 5], $inserted->toArray());
    }

    public function testInsertMany(): void
    {
        $collection = new Collection(new ArrayIterator([1, 5, 6, 7]));
        $inserted = $collection->insert(1, 2, 3, 4);

        self::assertNotSame($inserted, $collection);
        self::assertSame([1, 5, 6, 7], $collection->toArray());
        self::assertSame([1, 2, 3, 4, 5, 6, 7], $inserted->toArray());
    }

    public function testSlice(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5, 6, 7]));
        $sliced = $collection->slice(1, 3);

        self::assertNotSame($sliced, $collection);
        self::assertSame([1, 2, 3, 4, 5, 6, 7], $collection->toArray());
        self::assertSame([2, 3, 4], $sliced->toArray());
    }

    public function testWhere(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5, 6, 7]));
        $filtered = $collection->where(function($item){
            return $item % 2 > 0;
        });

        self::assertNotSame($filtered, $collection);
        self::assertSame([1, 2, 3, 4, 5, 6, 7], $collection->toArray());
        self::assertSame([1, 3, 5, 7], $filtered->toArray());
    }
}

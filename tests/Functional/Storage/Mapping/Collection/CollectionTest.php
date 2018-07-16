<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Collection;

use Igni\Exception\OutOfBoundsException;
use Igni\Storage\Exception\CollectionException;
use Igni\Storage\Mapping\Collection\Collection;
use ArrayIterator;
use IgniTest\Functional\Storage\StorageTrait;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    use StorageTrait;

    protected function setUp(): void
    {
        $this->setupStorage();
    }

    protected function tearDown(): void
    {
        $this->clearStorage();
    }

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

    public function testFailWhileInsertInNegativeIndex(): void
    {
        $this->expectException(CollectionException::class);
        $collection = new Collection(new ArrayIterator([1, 3, 4, 5]));
        $collection->insert(-1, 2);
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

    public function testCount(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new Collection($cursor);
        self::assertCount(10, $collection);
    }

    public function testIteration(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new Collection($cursor);
        $current = $collection->current();
        self::assertEquals(1, $current['TrackId']);
        self::assertSame(0, $collection->key());

        $current = $collection->current();
        self::assertEquals(1, $current['TrackId']);
        self::assertSame(0, $collection->key());

        $collection->next();
        $current = $collection->current();
        self::assertEquals(6, $current['TrackId']);
        self::assertSame(1, $collection->key());

        $current = $collection->at(4);
        self::assertEquals(9, $current['TrackId']);
        self::assertSame(1, $collection->key());

        $current = $collection->at(9);
        self::assertEquals(14, $current['TrackId']);
        self::assertSame(1, $collection->key());

        $current = $collection->first();
        self::assertEquals(1, $current['TrackId']);
        self::assertSame(0, $collection->key());

        $current = $collection->last();
        self::assertEquals(14, $current['TrackId']);
        self::assertSame(9, $collection->key());

        $collection->previous();
        $current = $collection->current();
        self::assertEquals(13, $current['TrackId']);
        self::assertSame(8, $collection->key());

        $collection->previous();
        $current = $collection->current();
        self::assertEquals(12, $current['TrackId']);
        self::assertSame(7, $collection->key());

        self::assertCount(10, $collection);

        $collection = new Collection($cursor);
        $collection->next();
        self::assertCount(10, $collection);

        $this->expectException(OutOfBoundsException::class);
        $collection->at(20);
    }

    public function testCurrent(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new Collection($cursor);
        $current = $collection->current();

        self::assertEquals(1, $current['TrackId']);
    }

    public function testAdd(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $item = [
            'TrackId' => 42,
        ];
        $collection = new Collection($cursor);
        $collection = $collection
            ->add($item)
            ->add($item)
            ->add($item)
            ->add($item);

        $length = self::readAttribute($collection, 'length');

        self::assertSame(14, $length);

        self::assertTrue($collection->contains($item));
        self::assertCount(14, $collection);

        $collection->rewind();

        $i = 0;
        foreach($collection as $item) {
            self::assertArrayHasKey('TrackId', $item);
            ++$i;
        }

        self::assertSame(14, $i);
    }

    public function testAddMany(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3]));
        $added = $collection->addMany(4, 5, 6);

        self::assertSame([1, 2, 3, 4, 5, 6], $added->toArray());
        self::assertSame([1, 2, 3], $collection->toArray());
        self::assertNotSame($added, $collection);
    }

    public function testEvery(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks;');
        $collection = new Collection($cursor);

        self::assertTrue($collection->every(function($item) {
            return isset($item['TrackId']);
        }));

        self::assertFalse($collection->every(function($item) {
            return isset($item['NonExistingField']);
        }));
    }

    public function testAny(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks;');
        $collection = new Collection($cursor);

        self::assertTrue($collection->any(function($item) {
            return $item['TrackId'] === '4';
        }));

        self::assertFalse($collection->any(function($item) {
            return $item['TrackId'] === 'a';
        }));
    }

    public function testReverse(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5, 6, 7]));
        $reversed = $collection->reverse();

        self::assertNotSame($reversed, $collection);
        self::assertSame([1, 2, 3, 4, 5, 6, 7], $collection->toArray());
        self::assertSame([7, 6, 5, 4, 3, 2, 1], $reversed->toArray());
    }

    public function testRemove(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5, 6, 7]));
        $removed = $collection->remove(2);

        self::assertNotSame($removed, $collection);
        self::assertSame([1, 2, 3, 4, 5, 6, 7], $collection->toArray());
        self::assertSame([1, 3, 4, 5, 6, 7], $removed->toArray());
    }

    public function testRemoveMany(): void
    {
        $collection = new Collection(new ArrayIterator([1, 2, 3, 4, 5, 6, 7]));
        $removed = $collection->removeMany(2, 5);

        self::assertNotSame($removed, $collection);
        self::assertSame([1, 2, 3, 4, 5, 6, 7], $collection->toArray());
        self::assertSame([1, 3, 4, 6, 7], $removed->toArray());
    }

    public function testClear(): void
    {
        $collection = new Collection(new ArrayIterator([1]));
        $collection->clear();
        self::assertSame([], $collection->toArray());
    }

}

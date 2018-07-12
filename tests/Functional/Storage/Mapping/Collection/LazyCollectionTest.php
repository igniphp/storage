<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Collection;

use Igni\Storage\Mapping\Collection\LazyCollection;
use IgniTest\Functional\Storage\StorageTrait;
use PHPUnit\Framework\TestCase;

final class LazyCollectionTest extends TestCase
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

    public function testCanCreate(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks');
        $collection = new LazyCollection($cursor);

        self::assertInstanceOf(LazyCollection::class, $collection);
    }

    public function testCount(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');
        $collection = new LazyCollection($cursor);
        self::assertCount(10, $collection);
    }

    public function testToArray(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');
        $collection = new LazyCollection($cursor);

        $items = $collection->toArray();
        self::assertCount(10, self::readAttribute($collection, 'items'));
        self::assertCount(10, $items);

        foreach ($items as $index => $item) {
            self::assertEquals($item, $collection->at($index));
        }
    }

    public function testManualIteration(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');
        $collection = new LazyCollection($cursor);
        self::assertCount(0, self::readAttribute($collection, 'items'));

        self::assertSame(0, $collection->key());
        $current = $collection->current();
        self::assertEquals(1, $current['TrackId']);
        self::assertCount(1, self::readAttribute($collection, 'items'));

        $collection->next();
        $current = $collection->current();
        self::assertEquals(6, $current['TrackId']);
        self::assertCount(2, self::readAttribute($collection, 'items'));

        $collection->next();
        $current = $collection->current();
        self::assertEquals(7, $current['TrackId']);
        self::assertCount(3, self::readAttribute($collection, 'items'));

        $current = $collection->at(5);
        self::assertEquals(10, $current['TrackId']);
        self::assertCount(6, self::readAttribute($collection, 'items'));

        $current = $collection->last();
        self::assertEquals(14, $current['TrackId']);
        self::assertCount(10, self::readAttribute($collection, 'items'));
        self::assertTrue(self::readAttribute($collection, 'complete'));
        self::assertNull(self::readAttribute($collection, 'cursor'));

        $current = $collection->first();
        self::assertEquals(1, $current['TrackId']);
        self::assertCount(10, self::readAttribute($collection, 'items'));

        $current = $collection->at(2);
        self::assertEquals(7, $current['TrackId']);
        self::assertCount(10, self::readAttribute($collection, 'items'));
    }

    public function testAutomaticIteration(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');
        $collection = new LazyCollection($cursor);
        $key = 0;
        foreach ($collection as $item) {
            self::assertArrayHasKey('TrackId', $item);
            self::assertCount(9, $item);
            self::assertSame($key++, $collection->key());
        }

        self::assertCount(10, self::readAttribute($collection, 'items'));
        self::assertCount(10, $collection);
    }
}

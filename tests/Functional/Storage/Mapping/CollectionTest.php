<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping;

use Igni\Storage\Mapping\Collection;
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

    public function testCanCreate(): void
    {

        $cursor = $this->createCursorForSql('SELECT *FROM tracks');

        $collection = new Collection($cursor);

        self::assertInstanceOf(Collection::class, $collection);
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
        $current = $collection->current();
        self::assertEquals(1, $current['TrackId']);
        $collection->next();
        $current = $collection->current();
        self::assertEquals(6, $current['TrackId']);

        $current = $collection->at(4);
        self::assertEquals(9, $current['TrackId']);

        $current = $collection->at(9);
        self::assertEquals(14, $current['TrackId']);

        $current = $collection->first();
        self::assertEquals(1, $current['TrackId']);

        $current = $collection->last();
        self::assertEquals(14, $current['TrackId']);

        self::assertCount(10, $collection);

        $collection = new Collection($cursor);
        $collection->next();
        self::assertCount(10, $collection);
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
        $collection->add($item);
        $collection->add($item);
        $collection->add($item);
        $collection->add($item);

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
}

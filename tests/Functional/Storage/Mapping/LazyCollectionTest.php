<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping;

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

        self::assertCount(10, $items);

        foreach ($items as $index => $item) {
            self::assertEquals($item, $collection->at($index));
        }
    }

    public function testCurrent(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new LazyCollection($cursor);
        $current = $collection->current();

        self::assertEquals(1,$current['TrackId']);
    }
}

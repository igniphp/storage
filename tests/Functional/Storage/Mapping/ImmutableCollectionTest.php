<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping;

use Igni\Storage\Mapping\ImmutableCollection;
use Igni\Utils\TestCase;
use IgniTest\Functional\Storage\StorageTrait;

final class ImmutableCollectionTest extends TestCase
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

        $collection = new ImmutableCollection($cursor);

        self::assertInstanceOf(ImmutableCollection::class, $collection);
    }

    public function testCount(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new ImmutableCollection($cursor);
        self::assertCount(10, $collection);
    }

    public function testToArray(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new ImmutableCollection($cursor);
        $items = $collection->toArray();

        self::assertCount(10, $items);

        foreach ($items as $index => $item) {
            self::assertEquals($item, $collection->at($index));
        }
    }

    public function testCurrent(): void
    {
        $cursor = $this->createCursorForSql('SELECT *FROM tracks WHERE AlbumId = 1;');

        $collection = new ImmutableCollection($cursor);
        $current = $collection->current();

        self::assertEquals(1,$current['TrackId']);
    }
}

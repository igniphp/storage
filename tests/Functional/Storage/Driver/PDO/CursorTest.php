<?php declare(strict_types=1);

namespace OcotpusTest\Functional\Storage\Driver\PDO;

use Igni\Storage\Driver\PDO\Cursor;
use Igni\Tests\Functional\Storage\StorageTrait;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    use StorageTrait;

    public function setUp()
    {
        parent::setUp();
        $this->setupStorage();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->clearStorage();
    }

    public function testCanInstantiate(): void
    {
        $cursor = $this->createCursorForSql('SELECT * FROM tracks');
        self::assertInstanceOf(Cursor::class, $cursor);
    }

    public function testCanIterate(): void
    {
        $cursor = $this->createCursorForSql('SELECT * FROM tracks');
        $count = 0;
        foreach ($cursor as $item) {
            $count++;
            self::assertArrayHasKey('TrackId', $item);
            self::assertArrayHasKey('Name', $item);
            self::assertArrayHasKey('AlbumId', $item);
            self::assertArrayHasKey('MediaTypeId', $item);
            self::assertArrayHasKey('GenreId', $item);
        }

        self::assertSame(3503, $count);
    }

    private function createCursorForSql(string $sql, array $bind = null)
    {
        return new Cursor($this->sqliteConnection, $sql, $bind);
    }
}

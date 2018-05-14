<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Driver\MongoDB;

use PHPUnit\Framework\TestCase;
use IgniTest\Functional\Storage\StorageTrait;

class ConnectionTest extends TestCase
{
    use StorageTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupStorage();
        $this->loadRepositories();
    }

    public function testCreate(): void
    {
        $this->mongoConnection->insert('playlist', [
            'name' => 'Test playlist',
            'details' => [
                'rating' => 1.0,
                'songs' => []
            ]
        ]);

        $cursor = $this->mongoConnection->find('playlist');
        $cursor->next();
        $cursor->current();
        $cursor->valid();

        $data = $cursor->toArray();

        self::assertCount(6, $data);
    }

    public function testDelete(): void
    {
        $this->mongoConnection->remove('playlist', 1);

        $cursor = $this->mongoConnection->find('playlist');
        $data = $cursor->toArray();

        self::assertCount(4, $data);
    }

    public function testFind(): void
    {
        $cursor = $this->mongoConnection->find('playlist');
        $data = $cursor->toArray();

        self::assertCount(5, $data);

        $cursor = $this->mongoConnection->find('playlist', ['name' => ['$regex' => 'composer']]);
        $data = $cursor->toArray();

        self::assertCount(1, $data);

        $cursor = $this->mongoConnection->find('playlist', ['details.rating' => ['$gt' => 3]]);
        $data = $cursor->toArray();

        self::assertCount(3, $data);

    }

    public function testUpdate(): void
    {
        $playlist = $this->mongoConnection->find('playlist', ['_id' => 1])->toArray()[0];

        $playlist['name'] = 'Rock Kafe pt. I';

        $this->mongoConnection->update('playlist', $playlist);
        $playlistCopy = $this->mongoConnection->find('playlist', ['_id' => 1])->toArray()[0];

        self::assertSame($playlist, $playlistCopy);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->clearStorage();
    }
}

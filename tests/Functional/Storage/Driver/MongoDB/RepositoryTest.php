<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Driver\MongoDB;

use Igni\Storage\Mapping\Collection;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Playlist\PlaylistEntity;
use IgniTest\Fixtures\Track\TrackEntity;
use IgniTest\Functional\Storage\StorageTrait;

class RepositoryTest extends TestCase
{
    use StorageTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupStorage();
        $this->loadRepositories();
    }

    public function testGet(): void
    {
        /** @var PlaylistEntity $playlist */
        $playlist = $this->entityManager->get(PlaylistEntity::class, 1);

        self::assertInstanceOf(PlaylistEntity::class, $playlist);

        $tracks = $playlist->getTracks();

        self::assertSame(1, $playlist->getId()->getValue());
        self::assertSame(4.12, $playlist->getRating());
        self::assertCount(12, $playlist->getTracks());

        self::assertInstanceOf(TrackEntity::class, $tracks->at(0));

    }

    public function testRemove(): void
    {
        $playlist = $this->entityManager->get(PlaylistEntity::class, 1);
        $this->entityManager->remove($playlist);

        $cursor = $this->mongoConnection->find('playlist', ['_id' => 1]);
        $dataset = $cursor->toArray();

        self::assertEmpty($dataset);
    }

    public function testCreate(): void
    {

        $playlist = new PlaylistEntity('playlistname');
        $playlist->addTrack($this->entityManager->get(TrackEntity::class, 1));
        $playlist->addTrack($this->entityManager->get(TrackEntity::class, 2));
        $playlist->addTrack($this->entityManager->get(TrackEntity::class, 3));

        $this->entityManager->create($playlist);

        $cursor = new Collection($this->mongoConnection->find('playlist', ['_id' => $playlist->getId()->getValue()]));
        self::assertEquals(
            [
                'id' => $playlist->getId()->getValue(),
                'name' => 'playlistname',
                'details' => [
                    'rating' => 0,
                    'songs' => [1, 2, 3],
                ],
            ],
            $cursor->current()
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->clearStorage();
    }
}

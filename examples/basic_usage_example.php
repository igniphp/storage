<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;
use Igni\Storage\Storage;
use Igni\Storage\Mapping\Collection\LazyCollection;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Storable;


// Define connections:
$sqliteConnection = new Connection(__DIR__ . '/db.db', new ConnectionOptions(
    $type = 'sqlite'
));
$sqliteConnection->open();


// Define entities:
/** @Entity(source="tracks") */
class TrackEntity implements Storable
{
    use AutoGenerateId;
    /** @Property\Id(class=GenericId::class, name="TrackId") */
    public $id;
    /** @Property\Text(name="Name") */
    public $title;
    /** @Property\Reference(ArtistEntity::class, name="ArtistId", readonly=true) */
    public $artist;

    public function __construct(ArtistEntity $artist, string $title)
    {
        $this->title = $title;
        $this->artist = $artist;
    }
}

/** @Entity(source="artists") */
class ArtistEntity implements Storable
{
    use AutoGenerateId;

    /** @Property\Id(class=GenericId::class, name="ArtistId") */
    public $id;

    /** @Property\Text() */
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

// Define repositories:

class TrackRepository extends Repository
{
    public function findByArtist(ArtistEntity $artist): LazyCollection
    {
        $cursor = $this->query("SELECT tracks.* FROM tracks JOIN albums ON albums.AlbumId = tracks.AlbumId JOIN artists ON artists.ArtistId = albums.ArtistId  WHERE albums.ArtistId = :id", [
            'id' => $artist->getId()
        ]);

        return new LazyCollection($cursor);
    }

    public function getEntityClass(): string
    {
        return TrackEntity::class;
    }
}

// Work with UnitOfWork:
$storage = new Storage();
$storage->addRepository(
    // Dynamic Repository
    new class($sqliteConnection, $storage->getEntityManager()) extends Repository {
        public function getEntityClass(): string
        {
            return ArtistEntity::class;
        }
    },

    // Custom Repository class
    new TrackRepository($sqliteConnection, $storage->getEntityManager())
);

$artist = $storage->get(ArtistEntity::class, 1);
$track = $storage->get(TrackEntity::class, 1);

// Find Artist's tracks
foreach ($storage->getRepository(TrackEntity::class)->findByArtist($artist) as $track) {
    echo $track->title;
}


// Override artist
$track->artist = $artist;

// Override artist name
$artist->name = 'John Lennon';

// Persist changes
$storage->persist($track, $artist);
$storage->commit();

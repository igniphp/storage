<?php declare(strict_types=1);
/**
 * Simple tutorial step by step introducing user to world of storage framework.
 * Each section contains comment explaining what is happening behind the scenes.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Storage;
use Igni\Storage\Mapping\Collection\Collection;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Storable;
use Igni\Storage\Driver\ConnectionManager;

// Below there are entities used in the example:

/**
 * Records are taken from `tracks` table and hydrated to `Track` instance
 * @Entity(source="tracks")
 */
class Track implements Storable
{
    use AutoGenerateId;
    /** @Property\Id(class=GenericId::class, name="TrackId") */
    public $id;
    /** @Property\Text(name="Name") */
    public $title;
    /** @Property\Reference(ArtistEntity::class, name="ArtistId", readonly=true) */
    public $artist;

    public function __construct(Artist $artist, string $title)
    {
        $this->title = $title;
        $this->artist = $artist;
    }
}

/**
 * Records are taken from `artists` table and hydrated to `Artist` instance
 * @Entity(source="artists")
 */
class Artist implements Storable
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

// The following lines contain repositories

/**
 * Repositories can be used to define complex queries and aggregations by using native SQL queries.
 */
class TrackRepository extends Repository
{
    /**
     * Finds all tracks that belongs to given artist and return results as collection.
     */
    public function findByArtist(Artist $artist): Collection
    {
        $cursor = $this->query("
            SELECT tracks.* 
            FROM tracks 
            JOIN albums ON albums.AlbumId = tracks.AlbumId 
            JOIN artists ON artists.ArtistId = albums.ArtistId  
            WHERE albums.ArtistId = :id
        ", [
            'id' => $artist->getId()
        ]);

        return new Collection($cursor);
    }

    public static function getEntityClass(): string
    {
        return Track::class;
    }
}
// Work with unit of work
// Define connections:
ConnectionManager::register(new Connection('sqlite:/' . __DIR__ . '/db.db'));

$storage = new Storage();

// Attach repositories
$storage->addRepository(
    // Dynamic Repository
    new class($storage->getEntityManager()) extends Repository {
        public static function getEntityClass(): string
        {
            return Artist::class;
        }
    },

    // Custom Repository class
    new TrackRepository($storage->getEntityManager())

);

// Fetch items from database

$artist = $storage->get(Artist::class, 1); // This is equivalent to: SELECT *FROM artists WHERE ArtistId = 1
$track = $storage->get(Track::class, 1); // This is equivalent to: SELECT *FROM tracks WHERE TrackId = 1

// Iterate through all tracks that belong to given artist
foreach ($storage->getRepository(Track::class)->findByArtist($artist) as $track) {
    echo $track->title;
}

// Create new artist.
$jimmy = new Artist('Moaning Jimmy');
$storage->persist($jimmy);

// Update track's artist.
$track->artist = $jimmy;

$storage->remove($artist); // This will remove existing artist with id 1 once commit is executed.

$storage->persist($track); // Save changes that will be flushed to database once commit is executed.

$storage->commit(); // All update queries will happen from this point on

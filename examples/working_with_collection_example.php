<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * This example will guide you through working with collection classes.
 * You will learn:
 * - how to locally map/reduce data
 * - how to work with low memory footprint
 */

use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Id;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property;
use Igni\Storage\Storable;
use Igni\Storage\Storage;

/**
 * This is our example entity class - nothing fancy.
 * All properties are public to simplify the example.
 * All items are taken from tracks table.
 * @Entity(source="tracks")
 */
class Track implements Storable
{
    /**
     * @Property(type="id", name="TrackId", class=GenericId::class)
     */
    public $id;

    /**
     * @Property(type="string", name="Name")
     */
    protected $name;

    public function getId(): Id
    {
        return $this->id;
    }
}


class TrackRepository extends Repository
{
    public static function getEntityClass(): string
    {
        return Track::class;
    }
}

ConnectionManager::register(new Connection('sqlite:/' . __DIR__ . '/db.db'));
$unitOfWork = new Storage();
$unitOfWork->addRepository(new TrackRepository($unitOfWork->getEntityManager()));

$track = $unitOfWork->get(Track::class, 1);

$track->getComposer();// Instance of composer.


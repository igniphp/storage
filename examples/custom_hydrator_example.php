<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use Igni\Storage\Storable;
use Igni\Storage\Storage;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Hydration\GenericHydrator;
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;
use Igni\Storage\Driver\Pdo\Repository;


final class TrackHydrator implements ObjectHydrator
{
    private $baseHydrator;

    public function __construct(GenericHydrator $baseHydrator)
    {
        $this->baseHydrator = $baseHydrator;
    }

    public function hydrate(array $data)
    {
        /** @var Track $entity */
        $entity = $this->baseHydrator->hydrate($data);
        $entity->setAlbum('Unknown album');
        // Here do custom hydration
        return $entity;
    }

    public function extract($entity): array
    {
        $data = $this->baseHydrator->extract($entity);
        // Here extract additional properties
        return $data;
    }
}

/**
 * @Entity(source="tracks", hydrator=TrackHydrator::class)
 */
class Track implements Storable
{
    use AutoGenerateId;

    /**
     * @Property(type="id", name="TrackId", class=GenericId::class)
     */
    protected $id;

    /**
     * @Property(type="string", name="Name")
     */
    protected $name;

    /**
     * @Property(name="Composer")
     */
    protected $composer;

    /**
     * @Property(type="int", name="Milliseconds")
     */
    protected $length;

    /**
     * @Property(type="int", name="Bytes")
     */
    protected $size;

    protected $album;

    public function __construct(string $name, string $composer)
    {
        $this->name = $name;
        $this->composer = $composer;
        $this->length = 0;
        $this->size = 0;
    }

    public function getComposer(): string
    {
        return $this->composer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setAlbum(string $album)
    {
        $this->album = $album;
    }

    public function getAlbum()
    {
        return $this->album;
    }
}

$sqlLiteConnection = new Connection(__DIR__ . '/db.db', new ConnectionOptions('sqlite'));
$sqlLiteConnection->open();
$unitOfWork = new Storage();
$unitOfWork->addRepository(new class($sqlLiteConnection, $unitOfWork->getEntityManager()) extends Repository {
    public function getEntityClass(): string
    {
        return Track::class;
    }
});

$track = $unitOfWork->get(Track::class, 1);

$track->getAlbum();// Unknown album.

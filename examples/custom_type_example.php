<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;
use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Storable;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Storage;


class ComposerType extends Property
{
    public function getType(): string
    {
        return 'composer';
    }
}

class ComposerMapping implements MappingStrategy
{
    public static function hydrate(&$value)
    {
        $value = new Composer($value);
    }

    public static function extract(&$value)
    {
        $value = (string) $value;
    }
}

Type::register('composer', ComposerMapping::class);


class Composer
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

/**
 * @Entity(source="tracks")
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
     * @Property(name="Composer", type="composer")
     */
    protected $composer;

    public function __construct(string $name, Composer $composer)
    {
        $this->name = $name;
        $this->composer = $composer;
    }

    public function getComposer(): Composer
    {
        return $this->composer;
    }

    public function getName(): string
    {
        return $this->name;
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

$track->getComposer();// Instance of composer.

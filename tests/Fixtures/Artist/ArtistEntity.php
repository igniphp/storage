<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\EntityManager;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\ImmutableCollection;
use IgniTest\Fixtures\Album\AlbumEntity;
use Igni\Storage\Mapping\Annotations as Storage;

/**
 * @Storage\Entity(source="artists", hydrator=ArtistHydrator::class)
 */
class ArtistEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Types\Text(name="Name")
     */
    protected $name;

    /**
     * @Types\ReferenceMany(class=AlbumEntity::class)
     */
    protected $albums;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function getAlbums(): ImmutableCollection
    {
        return $this->albums;
    }
}

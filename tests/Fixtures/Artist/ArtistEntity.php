<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\Annotations\Types as Property;
use Igni\Storage\Mapping\ImmutableCollection;

/**
 * @Storage\Entity(source="artists", hydrator=ArtistHydrator::class)
 */
class ArtistEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Property\Id(name="ArtistId")
     */
    protected $id;

    /**
     * @Property\Text(name="Name")
     */
    protected $name;

    /**
     * @Property\Delegate()
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

    public function setAlbums(ImmutableCollection $albums): void
    {
        $this->albums = $albums;
    }
}

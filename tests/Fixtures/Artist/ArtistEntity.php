<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Artist;

use Igni\Storage\Storable;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotation as Storage;
use Igni\Storage\Mapping\Annotation\Property as Property;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Storage\Mapping\Collection\LazyCollection;

/**
 * @Storage\Entity(source="artists", hydrator=ArtistHydrator::class)
 */
class ArtistEntity implements Storable
{
    use AutoGenerateId;

    /**
     * @Property\Id(GenericId::class, name="ArtistId")
     */
    protected $id;

    /**
     * @Property\Text(name="Name")
     */
    protected $name;

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

    public function getAlbums(): LazyCollection
    {
        return $this->albums;
    }

    public function setAlbums(LazyCollection $albums): void
    {
        $this->albums = $albums;
    }
}

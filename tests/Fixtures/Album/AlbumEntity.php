<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Album;

use DateTime;
use Igni\Storage\Storable;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotation as Storage;
use Igni\Storage\Mapping\Annotation\Property as Property;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Tests\Fixtures\Artist\ArtistEntity;

/**
 * @Storage\Entity(source="albums", hydrator=AlbumHydrator::class)
 */
class AlbumEntity implements Storable
{
    use AutoGenerateId;

    /**
     * @Property\Id(name="AlbumId", class=GenericId::class)
     */
    protected $id;

    /**
     * @Property\Reference(ArtistEntity::class, name="ArtistId")
     */
    protected $artist;

    /**
     * @Property\Text(name="Title")
     */
    protected $title;

    /**
     * @Property\Date(format="Ymd", name="ReleaseDate")
     */
    protected $releaseDate;

    protected $tracks;

    public function __construct(string $title, ArtistEntity $artist)
    {
        $this->title = $title;
        $this->artist = $artist;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getReleaseDate(): DateTime
    {
        return $this->releaseDate;
    }

    public function getArtist(): ArtistEntity
    {
        return $this->artist;
    }

    public function setTracks($tracks): void
    {
        $this->tracks = $tracks;
    }

    public function getTracks(): iterable
    {
        return $this->tracks ?? [];
    }
}

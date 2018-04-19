<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use DateTime;
use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\Annotations\Types as Property;
use IgniTest\Fixtures\Artist\ArtistEntity;

/**
 * @Storage\Entity(source="albums")
 */
class AlbumEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Property\Id(name="AlbumId")
     */
    protected $id;

    /**
     * @Property\Reference()
     */
    protected $artist;

    /**
     * @Property\Text()
     */
    protected $title;

    /**
     * @Property\Date(format="Ymd")
     */
    protected $releaseDate;

    /**
     * @Types\ReferenceMany(TrackEntity::class, repositoryMethod="findByAlbum")
     */
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

    public function getTracks(): iterable
    {
        return $this->tracks ?? [];
    }
}

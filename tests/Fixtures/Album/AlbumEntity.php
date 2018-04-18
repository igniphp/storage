<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use DateTime;
use IgniTest\Fixtures\Artist\ArtistEntity;

class AlbumEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Types\Reference()
     */
    protected $artist;

    /**
     * @Types\Id()
     */
    protected $id;

    protected $title;

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

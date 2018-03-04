<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\Entity;
use DateTime;
use IgniTest\Fixtures\Artist\ArtistEntity;

class AlbumEntity implements Entity
{
    protected $id;

    protected $artist;

    protected $title;

    protected $releaseDate;

    protected $tracks;

    public function __construct(string $id, string $title, ArtistEntity $artist)
    {
        $this->id = $id;
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

    public function getId(): string
    {
        return $this->id;
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

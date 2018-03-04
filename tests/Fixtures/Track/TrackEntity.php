<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Track;

use Igni\Storage\Entity;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Artist\ArtistEntity;
use IgniTest\Fixtures\Genre\GenreEntity;

class TrackEntity implements Entity
{
    const MEDIA_TYPE_AUDIO_MPEG = 1;
    const MEDIA_TYPE_PROTECTED_AAC = 2;
    const MEDIA_TYPE_VIDEO_MPEG= 3;
    const MEDIA_TYPE_UNLOCKED_AAC = 4;
    const MEDIA_TYPE_AUDIO_AAC = 5;

    protected $id;

    protected $name;

    protected $album;

    protected $composer;

    protected $length;

    protected $size;

    protected $unitPrice;

    protected $mediaType;

    protected $genre;

    protected $artist;

    public function __construct(int $id, string $name, string $composer, AlbumEntity $album)
    {
        $this->id = $id;
        $this->album = $album;
        $this->name = $name;
        $this->composer = $composer;
        $this->mediaType = self::MEDIA_TYPE_AUDIO_MPEG;
        $this->unitPrice = 0;
        $this->length = 0;
        $this->size = 0;
        $this->artist = $album->getArtist();
    }

    public function setPrice(float $price): void
    {
        $this->unitPrice = $price;
    }

    public function getPrice(): float
    {
        return $this->unitPrice;
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

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function setGenre(GenreEntity $genre): void
    {
        $this->genre = $genre;
    }

    public function getGenre(): GenreEntity
    {
        return $this->genre;
    }

    public function getAlbum(): AlbumEntity
    {
        return $this->album;
    }

    public function getArtist(): ArtistEntity
    {
        return $this->artist;
    }
}

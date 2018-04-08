<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Track;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Artist\ArtistEntity;
use IgniTest\Fixtures\Genre\GenreEntity;
use Igni\Storage\Mapping\Annotations as Storage;

/**
 * @Storage\Entity("tracks")
 */
class TrackEntity implements Entity
{
    use AutoGenerateId;

    const MEDIA_TYPE_AUDIO_MPEG = 1;
    const MEDIA_TYPE_PROTECTED_AAC = 2;
    const MEDIA_TYPE_VIDEO_MPEG= 3;
    const MEDIA_TYPE_UNLOCKED_AAC = 4;
    const MEDIA_TYPE_AUDIO_AAC = 5;

    /**
     * @Storage\Types\Text(name="Name")
     */
    protected $name;

    /**
     * @Storage\Types\Reference(AlbumEntity::class)
     */
    protected $album;

    /**
     * @Storage\Types\Text(name="Composer")
     */
    protected $composer;

    /**
     * @Storage\Type(name="Milliseconds", type="int")
     */
    protected $length;

    /**
     * @Storage\Types\IntegerNumber(name="Bytes")
     */
    protected $size;

    /**
     * @Storage\Types\FloatNumber(name="UnitPrice")
     */
    protected $unitPrice;

    /**
     * @Storage\Types\Enum({"Rock", "Jazz", "Metal", "Alternative", "Rock and Roll", "Blues", "Latin", "Reggae", "Pop"})
     */
    protected $mediaType;

    /**
     * @Storage\Types\Reference(GenreEntity::class)
     */
    protected $genre;

    /**
     * @Storage\Types\Reference(ArtistEntity::class)
     */
    protected $artist;

    public function __construct(string $name, string $composer, AlbumEntity $album)
    {
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

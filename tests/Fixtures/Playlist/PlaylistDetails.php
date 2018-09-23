<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Playlist;

use Igni\Storage\Mapping\Annotation as Storage;
use Igni\Storage\Mapping\Annotation\Property as Property;
use Igni\Storage\Mapping\Collection\LazyCollection;
use Igni\Tests\Fixtures\Track\TrackEntity;

/**
 * @Storage\EmbeddedEntity(hydrator=PlaylistDetailsHydrator::class)
 */
class PlaylistDetails
{
    /**
     * @Property\FloatNumber()
     */
    protected $rating = 0.0;

    protected $tracks = [];

    public function __construct(float $rating = 0.0)
    {
        $this->rating = $rating;
    }

    public function setTracks(LazyCollection $tracks): void
    {
        $this->tracks = $tracks;
    }

    public function getTracks()
    {
        return $this->tracks;
    }

    public function addTrack(TrackEntity $track): void
    {
        $this->tracks[] = $track;
    }

    public function getRating(): float
    {
        return $this->rating;
    }
}

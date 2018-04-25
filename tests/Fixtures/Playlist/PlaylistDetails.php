<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Mapping\ImmutableCollection;
use IgniTest\Fixtures\Track\TrackEntity;

use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\Annotations\Types as Property;

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

    public function setTracks(ImmutableCollection $tracks): void
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

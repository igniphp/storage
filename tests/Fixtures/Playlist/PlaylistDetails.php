<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use IgniTest\Fixtures\Track\TrackEntity;

use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\Annotations\Types as Property;

/**
 * @Storage\EmbeddedEntity(hydrator=PlaylistDetailsHydrator::class)
 */
class PlaylistDetails
{
    /**
     * @Property\Float()
     */
    protected $rating = 0.0;

    /**
     * @Property\Delegate()
     */
    protected $tracks = [];

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

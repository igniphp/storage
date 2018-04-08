<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use IgniTest\Fixtures\Track\TrackEntity;

/**
 * @EmbedEntity(hydrator=PlaylistDetailsHydrator::class)
 */
class PlaylistDetails
{
    /**
     * @Type\Float()
     */
    protected $rating = 0.0;

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

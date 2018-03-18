<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use IgniTest\Fixtures\Track\TrackEntity;

class PlaylistEntity implements Entity
{
    use AutoGenerateId;

    protected $name;

    /** @var PlaylistDetails */
    protected $details;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->details = new PlaylistDetails();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name)
    {
        $this->name = $name;
    }

    public function addTrack(TrackEntity $track): void
    {
        $this->details->addTrack($track);
    }

    public function getRating(): float
    {
        return $this->details->getRating();
    }

    public function getTracks()
    {
        return $this->details->getTracks();
    }
}

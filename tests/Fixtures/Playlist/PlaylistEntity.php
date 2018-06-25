<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Entity;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\Annotations\Types as Property;
use Igni\Storage\Mapping\AutoGenerateId;
use IgniTest\Fixtures\Track\TrackEntity;

/**
 * @Storage\Entity(source="playlist")
 */
class PlaylistEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Property\Id(class=GenericId::class)
     */
    protected $id;

    /**
     * @Property\Text()
     */
    protected $name;

    /**
     * @Property\Embed(PlaylistDetails::class, storeAs="plain")
     */
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

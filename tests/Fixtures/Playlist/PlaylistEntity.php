<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Storable;
use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\Annotation as Storage;
use Igni\Storage\Mapping\Annotation\Property as Property;
use Igni\Storage\Id\AutoGenerateId;
use IgniTest\Fixtures\Track\TrackEntity;

/**
 * @Storage\Entity(source="playlist")
 */
class PlaylistEntity implements Storable
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

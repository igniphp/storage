<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\ImmutableCollection;

class ArtistEntity implements Entity
{
    use AutoGenerateId;

    protected $name;
    protected $albums;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function getAlbums(): ImmutableCollection
    {
        return $this->albums;
    }
}

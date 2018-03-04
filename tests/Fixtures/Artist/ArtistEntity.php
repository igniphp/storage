<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\Entity;
use Igni\Storage\Mapping\ImmutableCollection;

class ArtistEntity implements Entity
{
    protected $name;

    protected $id;

    protected $albums;

    public function __construct(string $name, string $id)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAlbums(): ImmutableCollection
    {
        return $this->albums;
    }
}

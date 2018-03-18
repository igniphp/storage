<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\ImmutableCollection;

class GenreEntity implements Entity
{
    use AutoGenerateId;

    protected $name;

    protected $tracks;

    public function __construct(string $name, string $id)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTracks(): ImmutableCollection
    {
        return $this->tracks;
    }
}

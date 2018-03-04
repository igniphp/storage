<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\Entity;
use Igni\Storage\Mapping\ImmutableCollection;

class GenreEntity implements Entity
{
    protected $id;

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

    public function getId(): string
    {
        return $this->id;
    }

    public function getTracks(): ImmutableCollection
    {
        return $this->tracks;
    }

    public static function dupa()
    {
        $entity = new GenreEntity('aa', 'a');
        $entity->id = 'Dupa';
    }
}

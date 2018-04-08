<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\AutoGenerateId;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\ImmutableCollection;

/**
 * @Storage\Entity(source="genres")
 */
class GenreEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Storage\Type\Text(name="Name")
     */
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

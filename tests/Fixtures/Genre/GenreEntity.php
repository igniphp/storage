<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\Id\GenericId;
use Igni\Storage\Mapping\AutoGenerateId;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\Annotations as Storage;
use Igni\Storage\Mapping\Annotations\Types as Property;
use Igni\Storage\Mapping\ImmutableCollection;

/**
 * @Storage\Entity(source="genres")
 */
class GenreEntity implements Entity
{
    use AutoGenerateId;

    /**
     * @Property\Id(name="GenreId", class=GenericId::class)
     */
    protected $id;

    /**
     * @Property\Text(name="Name")
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

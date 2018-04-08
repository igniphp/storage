<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\Mapping\Type;
use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\NamingStrategy;
use Igni\Storage\Mapping\NamingStrategy\DirectNaming;
use Igni\Storage\Mapping\Strategy\Id;
use Igni\Storage\Mapping\Strategy\Text;
use IgniTest\Fixtures\Album\AlbumEntity;
use ReflectionProperty;
use ReflectionClass;

class ArtistHydrator implements ObjectHydrator
{
    /**
     * @var MappingStrategy[]
     */
    private $mapping;
    /**
     * @var ReflectionProperty[]
     */
    private $reflection;
    /**
     * @var ReflectionClass
     */
    private $class;
    /**
     * @var NamingStrategy
     */
    private $namingStrategy;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->class = new ReflectionClass(ArtistEntity::class);

        $this->reflection = [
            'name' => new ReflectionProperty(self::ENTITY_CLASS, 'name'),
            'albums' => new ReflectionProperty(self::ENTITY_CLASS, 'albums'),
            'id' => new ReflectionProperty(self::ENTITY_CLASS, 'id'),
        ];
    }

    public function hydrateAlbums(array $data, EntityManager $manager)
    {
        return $manager
            ->getRepository(AlbumEntity::class)
            ->findByArtistId($data['ArtistId']);
    }

    public function hydrate(array $data)
    {
        $instance = $this->class->newInstanceWithoutConstructor();

        $this->reflection['name']->setValue(
            $instance,
            Text::hydrate($data[$this->namingStrategy->map('name')])
        );

        $this->reflection['albums']->setValue(
            $instance,
            $this->hydrateAlbums($data, $this->entityManager)
        );

        $this->reflection['id']->setValue(
            $instance,
            Id::hydrate($data[$this->namingStrategy->map('id')])
        );

        return $instance;
    }

    public function extract($entity): array
    {
        return [
            $this->namingStrategy->map('id') => Id::extract($this->reflection['id']->getValue($entity)),
            $this->namingStrategy->map('name') => Text::extract($this->reflection['name']->getValue($entity)),
        ];
    }

    public function getSource(): string
    {
        return 'artists';
    }
}

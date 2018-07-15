<?php declare(strict_types=1);

namespace Igni\Storage;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Exception\RepositoryException;
use Igni\Storage\Hydration\HydratorAutoGenerate;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\IdentityMap;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\MetaData\MetaDataFactory;
use Igni\Storage\Mapping\MetaData\Strategy\AnnotationMetaDataFactory;
use Psr\SimpleCache\CacheInterface;

class EntityManager implements IdentityMap, MetaDataFactory
{
    /** @var Storable[] */
    private $registry = [];

    /** @var Repository[] */
    private $repositories = [];

    /** @var HydratorFactory */
    private $hydratorFactory;

    /** @var ObjectHydrator[] */
    private $hydrators = [];

    /** @var MetaDataFactory */
    private $metaDataFactory;

    /** @var string */
    private $hydratorDir;

    /** @var string */
    private $hydratorNamespace;

    /** @var CacheInterface */
    private $cache;

    /**
     * EntityManager constructor.
     * @param string|null $hydratorDir
     * @param string|null $hydratorNamespace
     * @param string|null $hydratorAutoGenerate
     * @param CacheInterface|null $cache
     * @param MetaDataFactory|null $metaDataFactory
     */
    public function __construct(
        string $hydratorDir = null,
        string $hydratorNamespace = null,
        string $hydratorAutoGenerate = HydratorAutoGenerate::IF_NOT_EXISTS,
        CacheInterface $cache = null,
        MetaDataFactory $metaDataFactory = null
    ) {
        if ($hydratorDir === null) {
            $hydratorDir = sys_get_temp_dir();
        }

        if (!is_writable($hydratorDir)) {
            throw new HydratorException("Hydrators cannot be generated, directory ($hydratorDir) is not writable.");
        }

        if ($metaDataFactory === null) {
            $metaDataFactory = new AnnotationMetaDataFactory();
        }

        if ($cache === null) {
            $cache = new ArrayCachePool();
        }

        $this->cache = $cache;
        $this->hydratorDir = $hydratorDir;
        $this->metaDataFactory = $metaDataFactory;
        $this->hydratorNamespace = $hydratorNamespace ?? '';
        $this->hydratorFactory = new HydratorFactory($this, $hydratorAutoGenerate);
    }

    public function getHydratorDir(): string
    {
        return $this->hydratorDir;
    }

    public function getHydratorNamespace(): string
    {
        return $this->hydratorNamespace;
    }

    /**
     * Creates new entity in the storage.
     *
     * @param Storable $entity
     * @return Storable
     */
    public function create(Storable $entity): Storable
    {
        $this->getRepository(get_class($entity))->create($entity);
        $this->attach($entity);

        return $entity;
    }

    /**
     * Updated entity in the storage.
     *
     * @param Storable $entity
     * @return Storable
     */
    public function update(Storable $entity): Storable
    {
        $this->getRepository(get_class($entity))->update($entity);

        return $entity;
    }

    /**
     * Removes entity from the storage.
     *
     * @param Storable $entity
     * @return Storable
     */
    public function remove(Storable $entity): Storable
    {
        $this->getRepository(get_class($entity))->remove($entity);
        $this->detach($entity);

        return $entity;
    }

    /**
     * Retrieves entity by identifier from the storage.
     *
     * @param string $entity
     * @param $id
     * @return Storable
     */
    public function get(string $entity, $id): Storable
    {
        $key = $this->getId($entity, $id);

        if ($this->has($entity, $id)) {
            return $this->registry[$key];
        }

        return $this->getRepository($entity)->get($id);
    }

    public function getRepository(string $entity): Repository
    {
        if ($this->hasRepository($entity)) {
            return $this->repositories[$entity];
        }

        throw RepositoryException::forNotRegisteredRepository($entity);
    }

    public function hasRepository(string $entity): bool
    {
        return isset($this->repositories[$entity]);
    }

    public function addRepository(Repository ...$repositories): void
    {
        foreach ($repositories as $repository) {
            $this->repositories[$repository::getEntityClass()] = $repository;
        }
    }

    public function attach(Storable $entity): Storable
    {
        $key = $this->getId($entity);

        if (!isset($this->registry[$key])) {
            $this->registry[$key] = $entity;
        }

        return $this->registry[$key];
    }

    public function detach(Storable $entity): Storable
    {
        $key = $this->getId($entity);
        if (isset($this->registry[$key])) {
            unset($this->registry[$key]);
        }

        return $entity;
    }

    /**
     *
     * @param string $class
     * @param $id
     * @return bool
     */
    public function has(string $class, $id): bool
    {
        $key = $this->getId($class, $id);
        return isset($this->registry[$key]);
    }

    /**
     * Checks if entity lives in the identity map.
     *
     * @param Storable $entity
     * @return bool
     */
    public function contains(Storable $entity): bool
    {
        return in_array($entity, $this->registry, true);
    }

    /**
     * Clears identity map.
     */
    public function clear(): void
    {
        $this->registry = [];
    }

    /**
     * Gets global id for entity which is used for
     * later storage in the identity map.
     *
     * @param $entity
     * @param null $id
     * @return string
     */
    private function getId($entity, $id = null): string
    {
        if ($entity instanceof Storable) {
            return get_class($entity) . '@' . $entity->getId()->getValue();
        }

        return "${entity}@${id}";
    }

    /**
     * Creates instance of the entity class and hydrates it with passed data.
     *
     * @param string $entityClass
     * @param array $data
     * @return object
     */
    public function hydrate(string $entityClass, array $data)
    {
        $hydrator = $this->getHydrator($entityClass);

        return $hydrator->hydrate($data);
    }

    /**
     * Extracts data from passed entity and returns it.
     *
     * @param $entity
     * @return array
     */
    public function extract($entity): array
    {
        $entityClass = get_class($entity);

        $hydrator = $this->getHydrator($entityClass);

        return $hydrator->extract($entity);
    }

    /**
     * Returns hydrator for the given entity.
     *
     * @param string $entity
     * @return ObjectHydrator
     */
    public function getHydrator(string $entity): ObjectHydrator
    {
        if (!isset($this->hydrators[$entity])) {
            $this->hydrators[$entity] = $this->hydratorFactory->get($entity);
        }

        return $this->hydrators[$entity];
    }

    /**
     * Returns entity's mapping metadata information
     *
     * @param string $entity
     * @return EntityMetaData
     */
    public function getMetaData(string $entity): EntityMetaData
    {
        $key = str_replace('\\', '.', $entity) . '.metadata';

        if (!$this->cache->has($key)) {
            $metaData = $this->metaDataFactory->getMetaData($entity);
            $this->cache->set($key, $metaData);

            return $metaData;
        }

        return $this->cache->get($key);
    }
}

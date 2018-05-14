<?php declare(strict_types=1);

namespace Igni\Storage;

use Cache\Adapter\Apc\ApcCachePool;
use Cache\Adapter\Apcu\ApcuCachePool;
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

class EntityManager implements IdentityMap, RepositoryContainer, MetaDataFactory
{
    /** @var Entity[] */
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
     * @param CacheInterface|null $cache
     * @param HydratorAutoGenerate|null $hydratorAutoGenerate
     * @param MetaDataFactory|null $metaDataFactory
     */
    public function __construct(
        string $hydratorDir = null,
        string $hydratorNamespace = null,
        CacheInterface $cache = null,
        HydratorAutoGenerate $hydratorAutoGenerate = null,
        MetaDataFactory $metaDataFactory = null
    ) {
        if ($hydratorDir === null) {
            $hydratorDir = sys_get_temp_dir();
        }

        if ($hydratorAutoGenerate === null) {
            $hydratorAutoGenerate = HydratorAutoGenerate::IF_NOT_EXISTS;
        }

        if (!is_writable($hydratorDir)) {
            throw new HydratorException("Hydrators cannot be generated, directory ($hydratorDir) is not writable.");
        }

        if ($metaDataFactory === null) {
            $metaDataFactory = new AnnotationMetaDataFactory();
        }

        if ($cache === null) {
            if (extension_loaded('apcu') && ini_get('apc.enabled')) {
                $cache = new ApcuCachePool();
            } elseif (extension_loaded('apc') && ini_get('apc.enabled')) {
                $cache = new ApcCachePool();
            } else {
                $cache = new ArrayCachePool();
            }
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
     * @param Entity $entity
     * @return Entity
     */
    public function create(Entity $entity): Entity
    {
        $this->getRepository(get_class($entity))->create($entity);
        $this->attach($entity);

        return $entity;
    }

    /**
     * Updated entity in the storage.
     *
     * @param Entity $entity
     * @return Entity
     */
    public function update(Entity $entity): Entity
    {
        $this->getRepository(get_class($entity))->update($entity);

        return $entity;
    }

    /**
     * Removes entity from the storage.
     *
     * @param Entity $entity
     * @return Entity
     */
    public function remove(Entity $entity): Entity
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
     * @return Entity
     */
    public function get(string $entity, $id): Entity
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

    public function addRepository(string $entity, Repository $repository): void
    {
        $this->repositories[$entity] = $repository;
    }

    public function attach(Entity $entity): Entity
    {
        $key = $this->getId($entity);

        if (!isset($this->registry[$key])) {
            $this->registry[$key] = $entity;
        }

        return $this->registry[$key];
    }

    public function detach(Entity $entity): Entity
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
     * @param Entity $entity
     * @return bool
     */
    public function contains(Entity $entity): bool
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
        if ($entity instanceof Entity) {
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
     * Returns entity's mapping metadata information.
     *
     * @param string|class $entity
     * @return EntityMetaData
     */
    public function getMetaData(string $entity): EntityMetaData
    {
        $key = str_replace('\\', '.', $entity) . '.metadata';

        if (!$this->cache->has($key)) {
            $metaData = $this->metaDataFactory->getMetaData($entity);
            $this->cache->set($key, $metaData);
        }

        return $this->cache->get($key);
    }
}

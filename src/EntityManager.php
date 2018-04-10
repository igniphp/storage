<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Exception\RepositoryException;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\HydratorGenerator\HydratorAutoGenerate;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\IdentityMap;
use Igni\Utils\ReflectionApi;

class EntityManager implements IdentityMap, RepositoryContainer
{
    /** @var Entity[] */
    private $registry;

    /** @var Repository[] */
    private $repositories = [];

    /** @var HydratorFactory */
    private $hydratorFactory;

    /** @var ObjectHydrator[] */
    private $hydrators = [];

    public function __construct(
        string $hydratorDir = null,
        string $hydratorNamespace = null,
        HydratorAutoGenerate $hydratorAutoGenerate = null
    ){
        if ($hydratorDir === null) {
            $hydratorDir = sys_get_temp_dir();
        }

        if ($hydratorAutoGenerate === null) {
            $hydratorAutoGenerate = HydratorAutoGenerate::IF_NOT_EXISTS();
        }

        if (!is_writable($hydratorDir)) {
            throw new HydratorException("Hydrators cannot be generated, directory ($hydratorDir) is not writable.");
        }

        $this->hydratorFactory = new HydratorFactory($hydratorDir, $hydratorAutoGenerate, $hydratorNamespace);
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

        return $this->registry[$key] = $this->getRepository($entity)->get($id);
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

    public function has(string $class, $id): bool
    {
        $key = $this->getId($class, $id);
        return isset($this->registry[$key]);
    }

    public function contains(Entity $entity): bool
    {
        return in_array($entity, $this->registry, true);
    }

    public function clear(): void
    {
        $this->registry = [];
    }

    private function getId($entity, $id = null): string
    {
        if ($entity instanceof Entity) {
            return get_class($entity) . '@' . $entity->getId();
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
        $instance = ReflectionApi::createInstance($entityClass);
        $hydrator = $this->getHydrator($entityClass);

        return $hydrator->hydrate($instance, $data);
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
            $hydrator = $this->hydratorFactory->get($entity);
            $this->hydrators[$entity] = new $hydrator($this);
        }

        return $this->hydrators[$entity];
    }
}

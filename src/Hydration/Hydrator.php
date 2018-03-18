<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\IdentityMap;
use Igni\Storage\Mapping\NamingStrategy;
use Igni\Storage\Mapping\Schema;
use Igni\Storage\Mapping\Strategy\DefinedMapping;
use Igni\Utils\ReflectionApi;

class Hydrator implements ObjectHydrator
{
    /** @var string|HydrationMode */
    private $mode;

    /** @var IdentityMap */
    protected $entityManager;

    /** @var NamingStrategy */
    protected $namingStrategy;

    /** @var Schema */
    protected $schema;

    public function __construct(EntityManager $entityManager, Schema $schema)
    {
        $this->entityManager = $entityManager;
        $this->schema = $schema;
        $this->mode = HydrationMode::BY_REFERENCE;
        $this->namingStrategy = $schema->getNamingStrategy();
    }

    public function setNamingStrategy(NamingStrategy $strategy)
    {
        $this->namingStrategy = $strategy;
    }

    public function getNamingStrategy(): NamingStrategy
    {
        return $this->namingStrategy;
    }

    public function hydrate(array $data)
    {
        $class = $this->schema->getEntity();
        /** @var Entity $entity */
        $entity = ReflectionApi::createInstance($class);

        $properties = $this->schema->getProperties();
        $namingStrategy = $this->getNamingStrategy();
        $manager = $this->entityManager;

        // This uses closure to bind entity context so protected properties can be overridden
        // the approach is faster than using reflection
        $hydrate = function($data) use ($properties, $namingStrategy, $entity, $manager, $class) {
            foreach($properties as $property => $strategy) {
                $key = $namingStrategy->map($property);
                $value = $data[$key] ?? null;

                if ($strategy instanceof DefinedMapping) {
                    $entity->{$property} = $strategy->hydrate($data, $manager);
                } else {
                    $entity->{$property} = $strategy->hydrate($value, $manager);
                }
            }
        };

        $hydrate->call($entity, $data);

        if ($this->mode === HydrationMode::BY_VALUE || !$entity instanceof Entity) {
            return $entity;
        }
        return $this->entityManager->attach($entity);
    }

    public function extract($entity): array
    {
        $properties = $this->schema->getProperties();
        $namingStrategy = $this->getNamingStrategy();
        $manager = $this->entityManager;

        $extractor = function($entity) use ($properties, $namingStrategy, $manager): array {
            // Id Autogeneration support.
            if ($entity instanceof Entity) {
                $entity->getId();
            }
            $extracted = [];
            foreach($properties as $property => $strategy) {
                if ($strategy instanceof DefinedMapping) {
                    if ($strategy->hasExtractor()) {
                        $return = $strategy->extract($entity);

                        if (is_array($return)) {
                            $extracted += $return;
                        }
                    }
                    continue;
                }
                $extracted[$namingStrategy->map($property)] = $strategy->extract($entity->{$property}, $manager);
            }

            return $extracted;
        };

        return $extractor->call($entity, $entity);
    }

    public function setMode(HydrationMode $mode): void
    {
        $this->mode = $mode->value();
    }
}

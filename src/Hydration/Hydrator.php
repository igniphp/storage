<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\Strategy\Delegator;
use Igni\Utils\ReflectionApi;

/**
 * @property EntityManager $entityManager
 */
trait Hydrator
{
    private $mode;

    public function hydrate(array $data)
    {
        $class = $this->getSchema()->getEntity();
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

                if ($strategy instanceof Delegator) {
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
                if ($strategy instanceof Delegator) {
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

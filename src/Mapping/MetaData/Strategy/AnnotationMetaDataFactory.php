<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData\Strategy;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\Annotation\EmbeddedEntity;
use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property as Property;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\MetaData\MetaDataFactory;
use Igni\Storage\Mapping\MetaData\PropertyMetaData;
use Igni\Storage\Mapping\Type;
use Igni\Utils\ReflectionApi;
use Psr\SimpleCache\CacheInterface;
use ReflectionProperty;
use ReflectionClass;

class AnnotationMetaDataFactory implements MetaDataFactory
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(CacheInterface $cache = null)
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
        $this->reader = new IndexedReader(new AnnotationReader());

        if ($cache === null) {
            $cache = new ArrayCachePool();
        }

        $this->cache = $cache;
    }

    public function getMetaData(string $entity): EntityMetaData
    {
        $cacheKey = str_replace('\\', '', $entity) . '.metadata';
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $metaData = $this->parseMetaData($entity);
        $this->cache->set($cacheKey, $metaData);

        return $metaData;
    }

    protected function parseMetaData(string $entityClass): EntityMetaData
    {
        $metaData = new EntityMetaData($entityClass);
        $reflection = ReflectionApi::reflectClass($entityClass);

        $this->parseClassAnnotations($reflection, $metaData);
        $this->parseProperties($reflection, $metaData);

        return $metaData;
    }

    private function parseClassAnnotations(ReflectionClass $reflection, EntityMetaData $metaData): void
    {
        $classAnnotations = $this->reader->getClassAnnotations($reflection);

        foreach ($classAnnotations as $type => $annotation) {
            switch ($type) {
                case Entity::class:
                    $source = $annotation->source ?? $annotation->value;
                    $metaData->setSource($source);
                    $metaData->setConnection($annotation->connection);
                    $this->setCustomHydrator($annotation, $metaData);
                    break;
                case EmbeddedEntity::class:
                    $metaData->makeEmbed();
                    $this->setCustomHydrator($annotation, $metaData);
                    break;
            }
        }
    }

    private function parseProperties(ReflectionClass $reflection, EntityMetaData $metaData): void
    {
        foreach ($reflection->getProperties() as $property) {
            $annotations = $this->reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Property) {
                    $this->addProperty($property, $annotation, $metaData);
                    break;
                }
            }
        }

        if (!$metaData->definesProperties()) {
            throw MappingException::forEmptyMapping($metaData->getClass());
        }
    }

    private function setCustomHydrator(Annotation $annotation, EntityMetaData $metaData)
    {
        if ($annotation->hydrator !== null) {
            if (!class_exists($annotation->hydrator)) {
                throw new MappingException("Cannot use hydrator {$annotation->hydrator} class does not exist.");
            }

            $metaData->setCustomHydratorClass($annotation->hydrator);
        }
    }

    private function addProperty(ReflectionProperty $property, Property $annotation, EntityMetaData $metaData): void
    {
        if (!Type::has($annotation->getType())) {
            throw new MappingException("Cannot map property {$property->getDeclaringClass()->getName()}::{$property->getName()} - unknown type {$annotation->getType()}.");
        }

        $property = new PropertyMetaData(
            $property->getName(),
            Type::get($annotation->getType())
        );
        $property->setFieldName($annotation->name ?? $property->getName());
        $property->setAttributes($annotation->getAttributes());
        $metaData->addProperty($property);
    }
}

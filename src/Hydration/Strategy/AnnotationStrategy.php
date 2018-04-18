<?php declare(strict_types=1);

namespace Igni\Storage\Hydration\Strategy;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Igni\Storage\Hydration\Strategy;
use Doctrine\Common\Annotations\AnnotationReader;
use Igni\Storage\Mapping\EntityMetaData;
use Igni\Utils\ReflectionApi;

class AnnotationStrategy
{
    private $annotationReader;
    private $namespace;

    public function __construct(string $namespace = null)
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
        $this->annotationReader = new IndexedReader(new AnnotationReader());
        $this->namespace = $namespace;
    }

    public function create(string $entityClass): EntityMetaData
    {
        $reflection = ReflectionApi::reflectClass($entityClass);
    }

    private function parseEntityAnnotations(array $annotations): void
    {

    }

    private function parsePropertyAnnotation(string $name, array $annotations): void
    {

    }
}

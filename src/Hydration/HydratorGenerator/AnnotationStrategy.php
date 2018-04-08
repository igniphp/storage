<?php declare(strict_types=1);

namespace Igni\Storage\Hydration\HydratorGenerator;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Igni\Storage\Hydration\HydratorGenerator;
use Doctrine\Common\Annotations\AnnotationReader;
use Igni\Utils\ReflectionApi;

class AnnotationStrategy implements HydratorGenerator
{
    private $annotationReader;
    private $namespace;

    public function __construct(string $namespace = null)
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
        $this->annotationReader = new IndexedReader(new AnnotationReader());
        $this->namespace = $namespace;
    }

    public function generate(string $entityClass): GeneratedHydrator
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

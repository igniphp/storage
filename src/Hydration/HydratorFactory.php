<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Utils\ReflectionApi;

final class HydratorFactory
{
    public const STRATEGY_ANNOTATION = 1;

    private $entityManager;
    private $autoGenerate;
    private $hydrators = [];

    public function __construct(
        EntityManager $entityManager,
        $autoGenerate = HydratorAutoGenerate::ALWAYS
    ) {
        $this->entityManager = $entityManager;
        $this->autoGenerate = $autoGenerate;
    }

    public function get(string $entityClass): ObjectHydrator
    {
        if (isset($this->hydrators[$entityClass])) {
            return $this->hydrators[$entityClass];
        }

        $entityMeta = $this->entityManager->getMetaData($entityClass);
        $hydratorClassName = $entityMeta->getHydratorClassName();
        $namespace = $this->entityManager->getHydratorNamespace();
        $hydratorClass = $namespace . '\\' . $hydratorClassName;

        // Fix for already loaded but not initialized hydrator.
        if (class_exists($hydratorClass)) {
            $objectHydrator = new $hydratorClass($this->entityManager);
            if ($entityMeta->definesCustomHydrator()) {
                $customHydratorClass = $entityMeta->getCustomHydratorClass();
                $objectHydrator = new $customHydratorClass($objectHydrator);
            }
            return $this->hydrators[$entityMeta->getClass()] = $objectHydrator;
        }

        $fileName = $this->entityManager->getHydratorDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $hydratorClassName) . '.php';
        switch ($this->autoGenerate) {
            case HydratorAutoGenerate::NEVER:

                require_once $fileName;
                break;

            case HydratorAutoGenerate::IF_NOT_EXISTS:

                if (!is_readable($fileName)) {
                    $hydrator = $this->create($entityMeta, true);
                    $this->writeHydrator($hydrator, $fileName);
                } else {
                    require_once $fileName;
                }
                break;

            case HydratorAutoGenerate::ALWAYS:
            default:
                $this->create($entityMeta, true);
                break;
        }

        $objectHydrator =  new $hydratorClass($this->entityManager);

        if ($entityMeta->definesCustomHydrator()) {
            $customHydratorClass = $entityMeta->getCustomHydratorClass();
            $objectHydrator = new $customHydratorClass($objectHydrator);
        }

        return $this->hydrators[$entityMeta->getClass()] = $objectHydrator;
    }

    private function create(EntityMetaData $metaData, bool $load = false): ReflectionApi\RuntimeClass
    {
        if ($this->entityManager->getHydratorNamespace() !== '') {
            $hydratorClass = ReflectionApi::createClass($this->entityManager->getHydratorNamespace() . '\\' . $metaData->getHydratorClassName());
        } else {
            $hydratorClass = ReflectionApi::createClass($metaData->getHydratorClassName());
        }
        $hydratorClass->extends(GenericHydrator::class);

        $getEntityClassMethod = new ReflectionApi\RuntimeMethod('getEntityClass');
        $getEntityClassMethod->makeStatic();
        $getEntityClassMethod->makeFinal();
        $getEntityClassMethod->setReturnType('string');
        $getEntityClassMethod->setBody(
            'return \\' . $metaData->getClass() . '::class;'
        );
        $hydratorClass->addMethod($getEntityClassMethod);

        $hydrateMethod = new ReflectionApi\RuntimeMethod('hydrate');
        $hydrateMethod->addArgument(new ReflectionApi\RuntimeArgument('data', 'array'));
        $hydrateMethod->setReturnType($metaData->getClass());
        $hydrateMethod->addLine('$entity = \\' . ReflectionApi::class . '::createInstance(self::getEntityClass());');
        $hydratorClass->addMethod($hydrateMethod);

        $extractMethod = new ReflectionApi\RuntimeMethod('extract');
        $extractMethod->addArgument(new ReflectionApi\RuntimeArgument('entity'));
        $extractMethod->setReturnType('array');
        $extractMethod->addLine("\$data = [];");
        $hydratorClass->addMethod($extractMethod);

        foreach ($metaData->getProperties() as $property) {
            /** @var MappingStrategy $type */
            $type = $property->getType();
            $attributes = $property->getAttributes();

            if (method_exists($type, 'getDefaultAttributes')) {
                $attributes += $type::getDefaultAttributes();
            }

            $attributes = preg_replace('/\s+/', '', var_export($attributes, true));

            // Build hydrator for property.
            $hydrateMethod->addLine("// Hydrate {$property->getName()}.");
            $hydrateMethod->addLine("\$value = \$data['{$property->getFieldName()}'] ?? null;");
            $hydrateMethod->addLine("\\{$type}::hydrate(\$value, ${attributes}, \$this->entityManager);");
            $hydrateMethod->addLine('\\' . ReflectionApi::class . "::writeProperty(\$entity, '{$property->getName()}', \$value);");

            // Store objects hydrated by reference
            $hydrateMethod->addLine("if (\$this->saveMemory === false && \$entity instanceof \Igni\Storage\Storable) {");
            $hydrateMethod->addLine("\t\$this->entityManager->attach(\$entity);");
            $hydrateMethod->addLine("}");

            // Build extractor for property.
            if (!$property->getAttributes()['readonly']) {
                $extractMethod->addLine("// Extract {$property->getName()}.");
                $extractMethod->addLine('$value = \\' . ReflectionApi::class . "::readProperty(\$entity, '{$property->getName()}');");
                $extractMethod->addLine("\\{$type}::extract(\$value, ${attributes}, \$this->entityManager);");
                $extractMethod->addLine("\$data['{$property->getFieldName()}'] = \$value;");
            }
        }

        $hydrateMethod->addLine('return $entity;');
        $extractMethod->addLine('return $data;');

        if ($load) {
            $hydratorClass->load();
        }

        return $hydratorClass;
    }

    private function writeHydrator(ReflectionApi\RuntimeClass $hydrator, string $uri): void
    {
        $temp = fopen($uri, 'w');

        if ($temp === false || !fwrite($temp, '<?php declare(strict_types=1);' . PHP_EOL . (string) $hydrator)) {
            throw new HydratorException("Could not write hydrator (${uri}) on disk - check for directory permissions.");
        }
        
        fclose($temp);
    }
}

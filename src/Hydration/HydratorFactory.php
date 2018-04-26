<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\MetaData\EntityMetaData;

final class HydratorFactory
{
    private const TEMPLATE = <<<EOF
<?php declare(strict_types = 1);
{namespace}

use Igni\Storage\Entity;
use Igni\Storage\Hydration\HydrationMode;
use Igni\Storage\Hydration\GenericHydrator;

final class {name} extends GenericHydrator
{
    public function hydrate(array \$data): \\{entity}
    {
        \$entityManager = \$this->entityManager;
        \$metaData = \$this->metaData;
        \$entity = \$metaData->createInstance();
        {hydrator}
        if (\$this->getMode() === HydrationMode::BY_REFERENCE && \$entity instanceof Entity) {
            \$this->entityManager->attach(\$entity);
        }
        return \$entity;
    }
    
    public function extract(\$entity): array
    {
        \$entityManager = \$this->entityManager;
        \$metaData = \$this->metaData;
        \$data = [];
        {extractor}
        
        return \$data;
    }
    
    public function getEntityClass(): string
    {
        return \\{entity}::class;
    }
}
EOF;

    public const STRATEGY_ANNOTATION = 1;

    private $entityManager;
    private $autoGenerate;
    private $hydrators = [];

    public function __construct(
        EntityManager $entityManager,
        HydratorAutoGenerate $autoGenerate
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
        switch ($this->autoGenerate->value()) {
            case HydratorAutoGenerate::ALWAYS:
                $this->create($entityMeta, true);
                break;

            case HydratorAutoGenerate::NEVER:

                require_once  $fileName;
                break;

            case HydratorAutoGenerate::IF_NOT_EXISTS:
            default:

                if (!is_readable($fileName)) {
                    $hydrator = $this->create($entityMeta, true);
                    $this->writeHydrator($hydrator, $fileName);
                } else {
                    require_once $fileName;
                }
                break;
        }

        $objectHydrator =  new $hydratorClass($this->entityManager);

        if ($entityMeta->definesCustomHydrator()) {
            $customHydratorClass = $entityMeta->getCustomHydratorClass();
            $objectHydrator = new $customHydratorClass($objectHydrator);
        }

        return $this->hydrators[$entityMeta->getClass()] = $objectHydrator;
    }

    private function create(EntityMetaData $metaData, bool $load = false): string
    {
        $hydrator = [];
        $extractor = [];
        $constructor = [];

        $compiled = self::TEMPLATE;

        $compiled = str_replace('{name}', $metaData->getHydratorClassName(), $compiled);
        $compiled = str_replace('{entity}', $metaData->getClass(), $compiled);

        $namespace = $this->entityManager->getHydratorNamespace();
        if ($namespace !== '') {
            $namespace = "namespace ${namespace};";
        } else {
            $namespace = '';
        }

        $compiled = str_replace('{namespace}', $namespace, $compiled);

        foreach ($metaData->getProperties() as $property) {
            /** @var MappingStrategy $type */
            $type = $property->getType();
            $attributes = $property->getAttributes();

            if (method_exists($type, 'getDefaultAttributes')) {
                $attributes += $type::getDefaultAttributes();
            }

            $attributes = preg_replace('/\s+/', '', var_export($attributes, true));

            // Build hydrator for property.
            $hydrator[] = "// Hydrate {$property->getName()}.";
            $hydrator[] = "\$value = \$data['{$property->getFieldName()}'] ?? null;";
            $hydrator[] = "\$attributes = ${attributes};";
            $hydrator[] = $type::getHydrator();
            $hydrator[] = "\$metaData->getProperty('{$property->getName()}')->setValue(\$entity, \$value);";

            // Build extractor for property.
            $extractor[] = "// Extract {$property->getName()}.";
            $extractor[] = "\$value = \$metaData->getProperty('{$property->getName()}')->getValue(\$entity);";
            $extractor[] = "\$attributes = ${attributes};";
            $extractor[] = $type::getExtractor();
            $extractor[] = "\$data['{$property->getFieldName()}'] = \$value;";
        }

        $compiled = str_replace('{constructor}', implode("\n        ", $constructor), $compiled);
        $compiled = str_replace('{hydrator}', implode("\n        ", $hydrator), $compiled);
        $compiled = str_replace('{extractor}', implode("\n        ", $extractor), $compiled);

        if ($load) {
            $this->loadHydrator($compiled);
        }

        return $compiled;
    }

    private function writeHydrator(string $hydrator, string $uri): void
    {
        $temp = fopen($uri, 'w');

        if ($temp === false || !fwrite($temp, $hydrator)) {
            throw new HydratorException("Could not write hydrator (${uri}) on disk - check for directory permissions.");
        }
        
        fclose($temp);
    }

    private function loadHydrator(string $hydrator): void
    {
        $uri = tempnam(sys_get_temp_dir(), 'igni');
        if (!$uri) {
            throw new HydratorException("Could not dynamically load hydrator");
        }
        $this->writeHydrator($hydrator, $uri);

        require_once $uri;
    }
}

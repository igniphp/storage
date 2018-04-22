<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Hydration\HydratorAutoGenerate;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\Strategy\Delegate;
use Igni\Utils\ReflectionApi;

final class HydratorFactory
{
    private const TEMPLATE = <<<EOF
<?php declare(strict_types = 1);
{namespace}

use Igni\Storage\Entity;
use Igni\Storage\EntityManager;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Hydration\ObjectHydrator;

final class {name}{extends} implements ObjectHydrator
{
    private \$__entityManager__;
    private \$__metaData__;
    
    public function __construct(EntityManager \$entityManager)
    {
        \$this->__entityManager__ = \$entityManager;
        \$this->__metaData__ = \$entityManager->getMetaData({entity}::class);
        {constructor}
    }
    
    public function hydrate(array \$data): {entity}
    {
        \$entityManager = \$this->__entityManager__;
        \$metaData = \$this->__metaData__;
        \$entity = \$metaData->createInstance();
        {hydrator}
        
        return \$entity;
    }
    
    public function extract(\$entity): array
    {
        \$entityManager = \$this->__entityManager__;
        \$metaData = \$this->__metaData__;
        \$data = [];
        {extractor}
        
        return \$data;
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
        $hydratorClass = $entityMeta->getHydratorClassName();

        // Fix for already loaded but not initialized hydrator.
        if (class_exists($hydratorClass)) {
            return $this->hydrators[$entityMeta->getClass()] = new $hydratorClass($this->entityManager);
        }

        $fileName = $this->entityManager->getHydratorDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $hydratorClass) . '.php';
        switch ($this->autoGenerate->value()) {
            case HydratorAutoGenerate::IF_NOT_EXISTS:
                if (!is_readable($fileName)) {
                    $hydrator = $this->create($entityMeta, true);
                    $this->writeHydrator($hydrator, $fileName);
                } else {
                    require_once $fileName;
                }

                return $this->hydrators[$entityMeta->getClass()] = new $hydratorClass($this->entityManager);

            case HydratorAutoGenerate::ALWAYS:
                $this->create($entityMeta, true);

                return $this->hydrators[$entityMeta->getClass()] = new $hydratorClass($this->entityManager);

            case HydratorAutoGenerate::NEVER:

                require_once  $fileName;
                return $this->hydrators[$entityMeta->getClass()] = new $hydratorClass($this->entityManager);
        }
    }

    private function create(EntityMetaData $metaData, bool $load = false): string
    {
        $hydrator = [];
        $extractor = [];
        $constructor = [];
        $delegator = [];

        $compiled = self::TEMPLATE;

        $compiled = str_replace('{name}', $metaData->getHydratorClassName(), $compiled);
        $compiled = str_replace('{entity}', $metaData->getClass(), $compiled);

        $namespace = $this->entityManager->getHydratorNamespace();
        $namespace = $namespace === '\\' ? '' : "namespace ${namespace}";
        $compiled = str_replace('{namespace}', $namespace, $compiled);

        if ($metaData->hasParentHydratorClass()) {
            $parentHydrator = $metaData->getParentHydratorClass();
            $compiled = str_replace('{extends}', " extends \\${parentHydrator}", $compiled);
            if (method_exists($parentHydrator, '__construct')) {
                $constructor[] = 'parent::__construct($entityManager);';
            }
        } else {
            $compiled = str_replace('{extends}', '', $compiled);
        }

        foreach ($metaData->getProperties() as $property) {
            /** @var MappingStrategy $type */
            $type = $property->getType();
            $attributes = $property->getAttributes();

            if (method_exists($type, 'getDefaultAttributes')) {
                $attributes += $type::getDefaultAttributes();
            }

            $attributes = preg_replace('/\s+/', '', var_export($attributes, true));

            // Delegator are handled at the end of the hydration process.
            if ($metaData->hasParentHydratorClass() && $type === Delegate::class) {
                $delegator[] = $property;
                continue;
            }

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

        // Handle delegator.
        foreach ($delegator as $property) {
            $hydratorDelegator = 'hydrate' . ucfirst($property->getName());
            $extractorDelegator = 'extract' . ucfirst($property->getName());

            if (method_exists($parentHydrator, $hydratorDelegator)) {
                $hydrator[] = "// Hydrate {$property->getName()}";
                $hydrator[] = "\$this->${hydratorDelegator}(\$entity, \$data);";
            }

            if (method_exists($parentHydrator, $extractorDelegator)) {
                $extractor[] = "// Extract {$property->getName()}";
                $extractor[] = "\$this->${extractorDelegator}(\$entity, \$data);";
            }
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

        if (!fwrite($temp, $hydrator)) {
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

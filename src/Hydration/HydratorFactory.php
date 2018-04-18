<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Hydration\Strategy\HydratorAutoGenerate;
use Igni\Storage\Mapping\EntityMetaData;
use Igni\Storage\Mapping\MappingStrategy;

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
    private \$entityManager;
    private \$reflectionProperties = [];
    
    public function __construct(EntityManager \$entityManager)
    {
        \$this->entityManager = \$entityManager;
        {properties}
    }
    
    public function hydrate(\$entity, array \$data): {entity}
    {
        {hydrator}
        
        return \$entity;
    }
    
    public function extract(\$entity): array
    {
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

    public function get(EntityMetaData $entityMeta): ObjectHydrator
    {
        if (isset($this->hydrators[$entityMeta->getClass()])) {
            return $this->hydrators[$entityMeta->getClass()];
        }

        $hydratorClass = $entityMeta->getHydratorClassName();
        $fileName = $this->entityManager->getHydratorDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $hydratorClass) . '.php';

        switch ($this->autoGenerate->value()) {
            case HydratorAutoGenerate::IF_NOT_EXISTS:
                if (!is_readable($fileName)) {
                    return $this->hydrators[$entityMeta->getClass()] = $this->create($entityMeta, $fileName);
                }

                require_once $fileName;

                return $this->hydrators[$entityMeta->getClass()] = new $hydratorClass($entityMeta);

            case HydratorAutoGenerate::ALWAYS:

                return $this->hydrators[$entityMeta->getClass()] = $this->create($entityMeta);

            case HydratorAutoGenerate::NEVER:
                require_once  $fileName;

                return $this->hydrators[$entityMeta->getClass()] = new $hydratorClass($entityMeta);
        }
    }

    public function create(EntityMetaData $metaData, bool $load = false): string
    {
        $entityClass = $metaData->getClass();
        $compiled = self::TEMPLATE;

        $compiled = str_replace('{name}', $metaData->getHydratorClassName(), $compiled);
        $compiled = str_replace('{entity}', $metaData->getClass(), $compiled);

        $namespace = $this->entityManager->getHydratorNamespace();
        $namespace = $namespace === '\\' ? '' : "namespace ${namespace}";
        $compiled = str_replace('{namespace}', $namespace, $compiled);

        if ($metaData->hasParentHydratorClass()) {
            $compiled = str_replace('{extends}', " extends {$metaData->getParentHydratorClass()}", $compiled);
        } else {
            $compiled = str_replace('{extends}', '', $compiled);
        }

        $hydrator = [];
        $extractor = [];
        $properties = [];

        foreach ($metaData->getProperties() as $name => $property) {
            /** @var MappingStrategy $type */
            $type = $property['type'];

            $properties[] = "\$this->reflectionProperties['${name}'] = new \ReflectionProperty('${entityClass}', '${name}');";
            $properties[] = "\$this->reflectionProperties['${name}']->setAccessible(true);";

            $attributes = $property['attributes'] ?? [];
            $attributes = preg_replace('/\s+/', '', var_export($attributes, true));

            if (method_exists($type, 'getDefaultAttributes')) {
                $attributes .= " + \\${type}::getDefaultAttributes()";
            }

            // Build hydrator for property.
            $hydrator[] = "// Hydrate ${name}.";
            $hydrator[] = "\$value = \$data['{$property['field']}'] ?? null;";
            $hydrator[] = "\$attributes = ${attributes};";
            $hydrator[] = $type::getHydrator();
            $hydrator[] = "\$this->reflectionProperties['${name}']->setValue(\$entity, \$value);";

            // Build extractor for property.
            $extractor[] = "// Extract ${name}.";
            $extractor[] = "\$value = \$this->reflectionProperties['${name}']->getValue(\$entity);";
            $extractor[] = "\$attributes = ${attributes};";
            $extractor[] = $type::getExtractor();
            $extractor[] = "\$data['{$property['field']}'] = \$value;";
        }

        $compiled = str_replace('{properties}', implode("\n        ", $properties), $compiled);
        $compiled = str_replace('{hydrator}', implode("\n        ", $hydrator), $compiled);
        $compiled = str_replace('{extractor}', implode("\n        ", $extractor), $compiled);

        if ($load) {
            $this->loadHydrator($compiled);
        }

        return $compiled;
    }

    private function loadHydrator(string $hydrator): void
    {
        $uri = tempnam(sys_get_temp_dir(), 'igni');
        if (!$uri) {
            throw new HydratorException("Could not dynamically load hydrator");
        }
        $temp = fopen($uri, 'w');

        if (!fwrite($temp, $hydrator)) {
            throw new HydratorException("Could not dynamically load hydrator- cannot write temporary file.");
        }
        fclose($temp);

        require_once $uri;
    }
}

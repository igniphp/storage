<?php declare(strict_types=1);

namespace Igni\Storage\Hydration\HydratorGenerator;

use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Mapping\Type;

/**
 * @internal This class is not supposed to be used in user-land.
 */
final class GeneratedHydrator
{
    private const TEMPLATE = <<<EOF
<?php declare(strict_types = 1);
{namespace}

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\MappingContext;

final class {name}{extends} implements ObjectHydrator
{
    private \$entityManager;
    private \$reflectionProperties = [];
    private \$mappingContext;
    private \$source;
    
    public function __construct(EntityManager \$entityManager)
    {
        \$this->entityManager = \$entityManager;
        \$this->mappingContext = new MappingContext({entity}::class, \$entityManager);
        \$this->source = {source} ?? '';
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
    
    public function getClass() : string
    {
        return {entity}::class;
    }
    
    public function hasSource(): bool
    {
        return \$this->source !== null;
    }
    
    public function getSource(): string
    {
        return \$this->source;
    }
}
EOF;

    private $compiled;
    private $properties = [];
    private $entityClass;
    private $className;
    private $namespace;
    private $parentClass;
    private $source;

    public function __construct(string $entityClass, string $namespace = null)
    {
        $this->entityClass = $entityClass;
        $this->className = $this->createHydratorName($entityClass);
        $this->namespace = $namespace;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function setParentHydrator(string $className): void
    {
        $this->parentClass = $className;
    }

    public function addProperty(string $name, string $type, array $attributes = []): void
    {
        if (!Type::has($type)) {
            throw new HydratorException("Cannot map property ${name} given type ${type} is not recognized.");
        }
        $this->properties[$name] = [
            'type' => '\\' . Type::get($type),
            'attributes' => $attributes,
            'field' => $attributes['field'] ?? $name,
        ];
    }

    public function compile(): string
    {
        if ($this->compiled !== null) {
            return $this->compiled;
        }

        $compiled = self::TEMPLATE;

        $compiled = str_replace('{entity}', '\\' . $this->entityClass, $compiled);
        $compiled = str_replace('{name}', $this->className, $compiled);
        $compiled = str_replace('{namespace}', $this->namespace ? "namespace {$this->namespace};" : '' , $compiled);

        if ($this->parentClass) {
            $compiled = str_replace('{extends}', " extends {$this->parentClass}", $compiled);
        } else {
            $compiled = str_replace('{extends}', '', $compiled);
        }

        if ($this->source) {
            $compiled = str_replace('{source}', "'{$this->source}'", $compiled);
        } else {
            $compiled = str_replace('{source}', 'null', $compiled);
        }

        $hydrator = [];
        $extractor = [];
        $properties = [];

        foreach ($this->properties as $name => $property) {
            $properties[] = "\$this->reflectionProperties['${name}'] = new \ReflectionProperty('{$this->entityClass}', '${name}');";
            $properties[] = "\$this->reflectionProperties['${name}']->setAccessible(true);";

            $attributes = preg_replace('/\s+/', '', var_export($property['attributes'], true));

            $value = "{$property['type']}::hydrate(\$data['{$property['field']}'] ?? null, \$this->mappingContext, {$attributes})";
            $hydrator[] = "\$this->reflectionProperties['${name}']->setValue(\$entity, ${value});";


            $value = "{$property['type']}::extract(\$this->reflectionProperties['${name}']->getValue(\$entity), \$this->mappingContext, {$attributes})";
            $extractor[] = "\$data['{$property['field']}'] = ${value};";
        }

        $compiled = str_replace('{properties}', implode("\n        ", $properties), $compiled);
        $compiled = str_replace('{hydrator}', implode("\n        ", $hydrator), $compiled);
        $compiled = str_replace('{extractor}', implode("\n        ", $extractor), $compiled);

        return $this->compiled = $compiled;
    }

    public function getClassName(): string
    {
        if ($this->namespace) {
            return $this->namespace . '\\' . $this->className;
        }

        return $this->className;
    }

    public function load(): void
    {
        $uri = tempnam(sys_get_temp_dir(), 'igniem');
        if (!$uri) {
            throw new HydratorException("Could not dynamically load hydrator");
        }
        $temp = fopen($uri, 'w');

        if (!fwrite($temp, (string) $this)) {
            throw new HydratorException("Could not dynamically load hydrator- cannot write temporary file.");
        }
        fclose($temp);

        require_once $uri;
    }

    private function createHydratorName(string $className): string
    {
        return str_replace('\\', '', $className) . 'Hydrator';
    }

    public function __toString(): string
    {
        $this->compile();

        return $this->compiled;
    }
}

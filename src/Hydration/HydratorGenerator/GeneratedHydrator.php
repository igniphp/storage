<?php declare(strict_types=1);

namespace Igni\Storage\Hydration\HydratorGenerator;

use Igni\Storage\Hydration\ObjectHydrator;

/**
 * @internal This class is not supposed to be used in user-land.
 */
final class GeneratedHydrator implements ObjectHydrator
{
    private const TEMPLATE = <<<EOF
<?php declare(strict_types = 1);

{namespace}

use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\MappingContext;
use ReflectionClass;

final class {class}{extends} implements ObjectHydrator
{
    private \$entityManager;
    private \$reflectionProperties;
    private \$reflectionClass;
    private \$mappingContext;
    
    public function __construct(EntityManager \$manager)
    {
        \$this->reflectionClass = new ReflectionClass({entity}::class);
        \$this->mappingContext = new MappingContext({entity}::class, \$this, \$manager);
        {properties}
    }
    
    public function hydrate(array \$data): {entity}
    {
        {hydrator}
    }
    
    public function extract(\$entity): array
    {
        {extractor}
    }
    
    public function getClass() : string
    {
        return {entity}::class;
    }
    
    {methods}
}

EOF;

    private $compiled;
    private $properties = [];
    private $methods = [];
    private $className;
    private $namespace;
    private $parentClass;
    private $source;

    public function __construct(string $className, string $namespace = null)
    {
        $this->className = $className;
        $this->namespace = $namespace;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
        $this->addMethod('getSource', [], "return '$source';");
    }

    public function setParentHydrator(string $className): void
    {
        $this->parentClass = $className;
    }

    public function addProperty(string $name, string $type, string $alias = null): void
    {
        $this->properties[$name] = [
            'type' => $type,
            'alias' => $alias,
        ];
    }

    public function addMethod(string $name, array $parameters = [], string $body): void
    {
        $this->methods[$name] = [
            'parameters' => $parameters,
            'body' => $body,
        ];
    }

    public function compile(): void
    {
        if ($this->compiled !== null) {
            return;
        }

        $compiled = self::TEMPLATE;

        $compiled = str_replace('{entity}', $this->className, $compiled);

        $compiled = str_replace('{namespace}', $this->namespace ? "namespace {$this->namespace};" : '' , $compiled);

        if ($this->parentClass) {
            $compiled = str_replace('{extends}', " extends {$this->parentClass}", $compiled);
        }

        $hydrator = '';
        $extractor = '';


        $this->compiled = $compiled;
    }

    public function hydrate(array $data)
    {
        // TODO: Implement hydrate() method.
    }

    public function extract($entity): array
    {
        // TODO: Implement extract() method.
    }

    public function __toString(): string
    {

    }
}

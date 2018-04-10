<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\Exception\StorageException;
use Igni\Storage\Hydration\HydratorGenerator\AnnotationStrategy;
use Igni\Storage\Hydration\HydratorGenerator\GeneratedHydrator;
use Igni\Storage\Hydration\HydratorGenerator\HydratorAutoGenerate;

/**
 * @internal
 */
final class HydratorFactory implements HydratorGenerator
{
    public const STRATEGY_ANNOTATION = 1;

    private $hydratorDir;
    private $autoGenerate;
    private $strategy;
    private $namespace;

    public function __construct(
        string $hydratorDir,
        HydratorAutoGenerate $autoGenerate,
        string $namespace = null,
        int $strategy = self::STRATEGY_ANNOTATION
    ) {
        $this->hydratorDir = $hydratorDir;
        $this->autoGenerate = $autoGenerate;
        $this->strategy = $strategy;
        $this->namespace = $namespace;
    }

    public function get(string $entity): string
    {

    }

    public function generate(string $entity): GeneratedHydrator
    {
        switch (self::STRATEGY_ANNOTATION) {
            case self::STRATEGY_ANNOTATION:
                return (new AnnotationStrategy($this->namespace))->generate($entity);
            default:
                throw new StorageException("Cannot generate hydrator for ${entity} - unknown strategy.");
        }
    }
}

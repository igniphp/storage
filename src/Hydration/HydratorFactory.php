<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\Exception\StorageException;
use Igni\Storage\Hydration\HydratorGenerator\AnnotationStrategy;
use Igni\Storage\Hydration\HydratorGenerator\GeneratedHydrator;
use Igni\Storage\Hydration\HydratorGenerator\HydratorAutoGenerate;

final class HydratorFactory implements HydratorGenerator
{
    public const STRATEGY_ANNOTATION = 1;

    private static $instance;

    private $hydratorDir;
    private $autoGenerate;
    private $strategy;
    private $namespace;

    private function __construct(
        string $hydratorDir,
        HydratorAutoGenerate $autoGenerate,
        string $namespace = null,
        int $strategy
    ) {
        $this->hydratorDir = $hydratorDir;
        $this->autoGenerate = $autoGenerate;
        $this->strategy = $strategy;
        $this->namespace = $namespace;
        $this->drivers = [
            new AnnotationStrategy($namespace),
        ];
    }

    public static function configure(
        string $hydratorDir,
        string $namespace = null,
        HydratorAutoGenerate $autoGenerate  = null
    ): void {
        if ($autoGenerate === null) {
            $autoGenerate = HydratorAutoGenerate::IF_NOT_EXISTS();
        }

        if (self::$instance !== null) {
            throw new StorageException('Cannot configure hydrator factory once instance has been created.');
        }

        self::$instance = new self($hydratorDir, $autoGenerate, $namespace);
    }

    public static function instance(): HydratorFactory
    {
        if (self::$instance === null) {
            self::$instance = new self(sys_get_temp_dir(), HydratorAutoGenerate::IF_NOT_EXISTS(), 1);
        }

        return self::$instance;
    }

    public function get(string $entity): ObjectHydrator
    {

    }

    public function generate(string $entity): GeneratedHydrator
    {
        switch (1) {
            case self::STRATEGY_ANNOTATION:
                return (new AnnotationStrategy($this->namespace))->generate($entity);
            default:
                throw new StorageException("Cannot generate hydrator for ${entity} - unknown strategy.");
        }
    }
}

<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Exception\MappingException;
use Igni\Storage\Hydration\Hydrator;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\Schema;

final class Embed implements MappingStrategy
{
    public const STORAGE_PLAIN = 'plain';
    public const STORAGE_SERIALIZED = 'serialized';
    public const STORAGE_JSON = 'json';

    private $embedSchema;
    private $hydrator;
    private $storageType;

    public function __construct(Schema $schema, string $storeAs = self::STORAGE_PLAIN)
    {
        $this->embedSchema = $schema;
        $this->storageType = $storeAs;
    }

    public function hydrate($value, EntityManager $manager = null)
    {
        if (!$this->hydrator) {
            $this->hydrator = new Hydrator($manager, $this->embedSchema);
        }
        switch ($this->storageType) {
            case self::STORAGE_JSON:
                $value = json_decode($value, true);
                break;
            case self::STORAGE_SERIALIZED:
                $value = unserialize($value);
                break;
            case self::STORAGE_PLAIN:
                break;
            default:
                throw MappingException::forUnknownMappingStrategy(Embed::class . '(storage=' . $this->storageType . ')');
        }
        return $this->hydrator->hydrate($value);
    }

    public function extract($value, EntityManager $manager = null)
    {
        if (!$this->hydrator) {
            $this->hydrator = new Hydrator($manager, $this->embedSchema);
        }
        $value = $this->hydrator->extract($value);
        switch ($this->storageType) {
            case self::STORAGE_JSON:
                $value = json_encode($value);
                break;
            case self::STORAGE_SERIALIZED:
                $value = serialize($value);
                break;
            case self::STORAGE_PLAIN:
                break;
            default:
                throw MappingException::forUnknownMappingStrategy(Embed::class . '(storage=' . $this->storageType . ')');
        }
        return $value;
    }
}

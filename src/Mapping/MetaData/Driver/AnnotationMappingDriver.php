<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Igni\Storage\Mapping\EntityMetaData;
use Igni\Storage\Mapping\MetaData\MappingDriver;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\ArrayCache;

class AnnotationMappingDriver implements MappingDriver
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(CacheInterface $cache = null, Reader $reader = null)
    {
        if ($cache === null) {
            $cache = new ArrayCache();
        }

        if ($reader === null) {
            $reader = new AnnotationReader();
        }

        $this->cache = $cache;
        $this->reader = $reader;
    }

    public function getMetaData(string $entity): EntityMetaData
    {

    }
}

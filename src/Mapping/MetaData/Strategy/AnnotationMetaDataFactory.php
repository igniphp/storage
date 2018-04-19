<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData\Strategy;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\MetaData\MetaDataFactory;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\ArrayCache;

class AnnotationMetaDataFactory implements MetaDataFactory
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(CacheInterface $cache = null)
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
        $this->reader = new IndexedReader(new AnnotationReader());

        if ($cache === null) {
            $cache = new ArrayCache();
        }

        $this->cache = $cache;
    }

    public function getMetaData(string $entity): EntityMetaData
    {

    }
}

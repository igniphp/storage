<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData;

interface MetaDataFactory
{
    public function getMetaData(string $entity): EntityMetaData;
}

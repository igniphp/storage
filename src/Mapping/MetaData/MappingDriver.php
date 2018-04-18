<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\MetaData;

use Igni\Storage\Mapping\EntityMetaData;

interface MappingDriver
{
    public function getMetaData(string $entity): EntityMetaData;
}

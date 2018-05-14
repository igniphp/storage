<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping;

use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\MetaData\PropertyMetaData;
use Igni\Storage\Mapping\Strategy\Text;
use IgniTest\Fixtures\Artist\ArtistEntity;
use PHPUnit\Framework\TestCase;

final class EntityMetaDataTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(EntityMetaData::class, new EntityMetaData(ArtistEntity::class));
    }

    public function testCreateInstance(): void
    {
        $metaData = new EntityMetaData(ArtistEntity::class);

        self::assertInstanceOf(ArtistEntity::class, $metaData->createInstance());
    }

    public function testSerializeAndUnserialize(): void
    {
        $metaData = new EntityMetaData(ArtistEntity::class);
        $metaData->setSource('test');
        $metaData->addProperty(new PropertyMetaData('Name', Text::class));

        $serialized = serialize($metaData);
        /** @var EntityMetaData $unserialized */
        $unserialized = unserialize($serialized);

        self::assertInstanceOf(EntityMetaData::class, $unserialized);
        self::assertSame(ArtistEntity::class, $unserialized->getClass());
        self::assertSame('test', $unserialized->getSource());
    }
}

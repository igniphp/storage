<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\MetaData\Strategy;

use Igni\Storage\Mapping\MetaData\Strategy\AnnotationMetaDataFactory;
use Igni\Storage\Mapping\Strategy\Embed;
use Igni\Storage\Mapping\Strategy\Id;
use Igni\Storage\Mapping\Strategy\Text;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Playlist\PlaylistEntity;

final class AnnotationMetaDataFactoryTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $factory = new AnnotationMetaDataFactory();
        self::assertInstanceOf(AnnotationMetaDataFactory::class, $factory);
    }

    public function testParseMetaData(): void
    {
        $factory = new AnnotationMetaDataFactory();
        $metaData = $factory->getMetaData(PlaylistEntity::class);

        self::assertCount(3, $metaData->getProperties());
        self::assertSame('playlist', $metaData->getSource());
        self::assertSame(PlaylistEntity::class, $metaData->getClass());
        self::assertFalse($metaData->isEmbed());

        $properties = $metaData->getProperties();
        self::assertCount(3, $properties);

        self::assertSame(Id::class, $metaData->getProperty('id')->getType());
        self::assertSame(Text::class, $metaData->getProperty('name')->getType());
        self::assertSame(Embed::class, $metaData->getProperty('details')->getType());
    }
}

<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping;

use Igni\Storage\Mapping\MetaData\PropertyMetaData;
use Igni\Storage\Mapping\Strategy\IntegerNumber;
use Igni\Storage\Mapping\Strategy\Text;
use IgniTest\Fixtures\Artist\ArtistEntity;
use PHPUnit\Framework\TestCase;

final class PropertyMetaDataTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(PropertyMetaData::class, new PropertyMetaData('test', IntegerNumber::class));
    }

    public function testSerializeProperty(): void
    {
        $property = new PropertyMetaData('name', Text::class);
        $property->setFieldName('Name');
        $property->setAttributes(['length' => 20]);
        $serialized = serialize($property);
        $unserialized = unserialize($serialized);

        self::assertSame('Name', $unserialized->getFieldName());
        self::assertSame('name', $unserialized->getName());
        self::assertSame(Text::class, $unserialized->getType());
        self::assertEquals(['length' => 20], $unserialized->getAttributes());
        $this->setAndGetName($unserialized);
    }

    public function testSetGetValue(): void
    {
        $property = new PropertyMetaData('name', Text::class);
        $this->setAndGetName($property);
    }

    private function setAndGetName(PropertyMetaData $property): void
    {
        $artist = new ArtistEntity('Test Name');

        self::assertSame('Test Name', $property->getValue($artist));
        $property->setValue($artist, 'Updated Name');
        self::assertSame('Updated Name', $property->getValue($artist));
    }
}

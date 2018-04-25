<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Mapping\Strategy;

use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\Strategy\Embed;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Playlist\PlaylistDetails;

final class EmbedTest extends TestCase
{
    public function testExtract(): void
    {
        $playlistDetails = new PlaylistDetails(2.0);
        $extracted = [
            'rating' => 2.0,
        ];
        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('extract')
            ->withArgs([$playlistDetails])
            ->andReturn($extracted);
        $value = $playlistDetails;

        $attributes = [
            'class' => PlaylistDetails::class,
        ] + Embed::getDefaultAttributes();
        eval(Embed::getExtractor());

        self::assertSame(Embed::serializeValue($extracted, 'json'), $value);
    }

    public function testExtractNull(): void
    {
        $value = null;

        $attributes = [
                'class' => PlaylistDetails::class,
            ] + Embed::getDefaultAttributes();
        eval(Embed::getExtractor());

        self::assertNull($value);
    }

    public function testHydrate(): void
    {
        $playlistDetails = new PlaylistDetails(2.0);
        $extracted = [
            'rating' => 2.0,
        ];
        $serialized = Embed::serializeValue($extracted, 'json');
        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('hydrate')
            ->withArgs([PlaylistDetails::class, $extracted])
            ->andReturn($playlistDetails);
        $value = $serialized;

        $attributes = [
                'class' => PlaylistDetails::class,
            ] + Embed::getDefaultAttributes();
        eval(Embed::getHydrator());

        self::assertSame($playlistDetails, $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;

        $attributes = [
                'class' => PlaylistDetails::class,
            ] + Embed::getDefaultAttributes();
        eval(Embed::getHydrator());

        self::assertNull($value);
    }
}

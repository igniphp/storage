<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage\Mapping\Strategy;

use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\Strategy\Embed;
use Igni\Tests\Fixtures\Playlist\PlaylistDetails;
use Mockery;
use PHPUnit\Framework\TestCase;

final class EmbedTest extends TestCase
{
    public function testExtract(): void
    {
        $playlistDetails = new PlaylistDetails(2.0);
        $extracted = [
            'rating' => 2.0,
        ];
        $entityManager = Mockery::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('extract')
            ->withArgs([$playlistDetails])
            ->andReturn($extracted);
        $value = $playlistDetails;

        $attributes = [
            'class' => PlaylistDetails::class,
        ] + Embed::getDefaultAttributes();
        Embed::extract($value, $attributes, $entityManager);

        self::assertSame(json_encode($extracted, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION), $value);
    }

    public function testExtractNull(): void
    {
        $value = null;

        $attributes = [
                'class' => PlaylistDetails::class,
            ] + Embed::getDefaultAttributes();
        Embed::extract($value, $attributes);

        self::assertNull($value);
    }

    public function testHydrate(): void
    {
        $playlistDetails = new PlaylistDetails(2.0);
        $extracted = [
            'rating' => 2.0,
        ];
        $serialized = json_encode($extracted, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $entityManager = Mockery::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('hydrate')
            ->withArgs([PlaylistDetails::class, $extracted])
            ->andReturn($playlistDetails);
        $value = $serialized;

        $attributes = [
                'class' => PlaylistDetails::class,
            ] + Embed::getDefaultAttributes();
        Embed::hydrate($value, $attributes, $entityManager);

        self::assertSame($playlistDetails, $value);
    }

    public function testHydrateNull(): void
    {
        $value = null;

        $attributes = [
                'class' => PlaylistDetails::class,
            ] + Embed::getDefaultAttributes();
        Embed::hydrate($value, $attributes);

        self::assertNull($value);
    }
}

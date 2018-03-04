<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\Strategy\DecimalNumber;
use Igni\Storage\Mapping\Strategy\DefinedMapping;
use Igni\Storage\Mapping\Strategy\Embed;
use Igni\Storage\Mapping\Strategy\FloatNumber;
use Igni\Storage\Mapping\Strategy\Id;
use Igni\Storage\Mapping\Strategy\IntegerNumber;
use Igni\Storage\Mapping\Strategy\Reference;
use Igni\Storage\Mapping\Strategy\Text;
use Igni\Storage\Mapping\Strategy\Date;

/**
 * @method static Id id()
 * @method static DecimalNumber decimal(int $scale = null, int $precision = null)
 * @method static FloatNumber float()
 * @method static IntegerNumber integer()
 * @method static Text string(int $length = null)
 * @method static Date date(string $format = "Ymd", string $timezone = "UTC")
 * @method static Embed embed(Schema $schema, $storeAs = 'plain')
 * @method static Reference reference(string $entity)
 * @method static DefinedMapping define(callable $hydrator, callable $extractor = null)
 */
final class Type
{
    private static $types = [
        'decimal' => DecimalNumber::class,
        'float' => FloatNumber::class,
        'id' => Id::class,
        'integer' => IntegerNumber::class,
        'string' => Text::class,
        'date' => Date::class,
        'reference' => Reference::class,
        'embed' => Embed::class,
        'define' => DefinedMapping::class,
    ];

    private function __construct() {}

    public static function register(string $type, string $class): void
    {
        self::$types[$type] = $class;
    }

    public static function __callStatic($name, $arguments): MappingStrategy
    {
        if (!isset(self::$types[$name])) {
            throw MappingException::forUnknownMappingStrategy($name);
        }

        $type = self::$types[$name];
        return new $type(...$arguments);
    }
}

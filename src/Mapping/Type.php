<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\Strategy;
use Igni\Storage\Mapping\Annotations\Types;

/**
 * @method static Id id()
 * @method static DecimalNumber decimal(int $scale = null, int $precision = null)
 * @method static FloatNumber float()
 * @method static IntegerNumber integer()
 * @method static Text string(int $length = null)
 * @method static Date date(string $format = "Ymd", string $timezone = "UTC")
 * @method static Embed embed(Schema $schema, $storeAs = 'plain')
 * @method static Reference reference(string $entity)
 */
final class Type
{
    private static $types = [
        Types\Date::class,
        'decimal' => Types\DecimalNumber::class,
        'embed' => Types\Embed::class,
        'float' => Types\FloatNumber::class,
        'id' => Types\Id::class,
        'integer' => Types\IntegerNumber::class,
        'string' => Types\Text::class,
        'reference' => Types\Reference::class,
    ];

    private static $aliases = [

    ];

    private function __construct() {}

    public static function register(string $type, string $class): void
    {
        self::$types[$type] = $class;
    }

    public static function addType(string $typeClass, string $mappingClass): void
    {

    }
}

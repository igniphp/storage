<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Mapping\Strategy;

final class Type
{
    private static $types = [
        'date' => Strategy\Date::class,
        'decimal' => Strategy\DecimalNumber::class,
        'embed' => Strategy\Embed::class,
        'float' => Strategy\FloatNumber::class,
        'id' => Strategy\Id::class,
        'integer' => Strategy\IntegerNumber::class,
        'text' => Strategy\Text::class,
        'string' => Strategy\Text::class,
        'reference' => Strategy\Reference::class,
    ];

    private function __construct() {}

    public static function register(string $type, string $class): void
    {
        self::$types[$type] = $class;
    }

    public static function has(string $type): bool
    {
        return isset(self::$types[$type]);
    }

    /**
     * @param string $type
     *
     * @return MappingStrategy|string
     */
    public static function get(string $type): string
    {
        return self::$types[$type];
    }
}

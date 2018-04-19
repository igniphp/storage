<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class DecimalNumber implements MappingStrategy, DefaultAttributesProvider
{
    public static function getHydrator(): string
    {
        return '
        $value = (string) $value;
        $value = explode(\'.\', $value);
        $value = substr($value[0], 0, $options[\'scale\']) . \'.\' . substr($value[1], 0, $attributes[\'precision\']);
        ';
    }

    public static function getExtractor(): string
    {
        return '
        $value = (string) $value;
        $value = explode(\'.\', $value);
        $value = substr($value[0], 0, $options[\'scale\']) . \'.\' . substr($value[1], 0, $attributes[\'precision\']);
        ';
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'scale' => 10,
            'precision' => 2,
        ];
    }
}

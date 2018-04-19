<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Utils\Enum;

/**
 * @method static HydratorAutoGenerate ALWAYS
 * @method static HydratorAutoGenerate NEVER
 * @method static HydratorAutoGenerate IF_NOT_EXISTS
 */
class HydratorAutoGenerate extends Enum
{
    public const ALWAYS = 'always';
    public const NEVER = 'never';
    public const IF_NOT_EXISTS = 'if_not_exists';
}

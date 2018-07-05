<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Annotation\Property;

use Igni\Storage\Mapping\Annotation\Property;

/**
 * @Annotation
 * @see \Igni\Storage\Mapping\Strategy\Date
 */
class Date extends Property
{
    public $timezone = 'UTC';

    public $format;

    public $immutable = false;

    public function getType(): string
    {
        return 'date';
    }

    public function getFormat(): string
    {
        return $this->format ?? $this->value ?? 'Ymd';
    }
}

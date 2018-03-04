<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;
use DateTime;
use DateTimeZone;

final class Date implements MappingStrategy
{
    private $timezone;
    private $format;

    public function __construct(string $format = 'Ymd', string $timezone = 'UTC')
    {
        $this->timezone = $timezone;
        $this->format = $format;
    }

    public function hydrate($value): ?DateTime
    {
        if ($value === null) {
            return null;
        }
        return new DateTime($value, new DateTimeZone($this->timezone));
    }

    /**
     * @param DateTime $value
     * @return string
     */
    public function extract($value): ?string
    {
        if ($value === null) {
            return $value;
        }
        return $value->format($this->format);
    }
}

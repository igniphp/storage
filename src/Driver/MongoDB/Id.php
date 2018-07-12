<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use MongoDB\BSON\ObjectId;

class Id implements \Igni\Storage\Id
{
    private $value;

    public function __construct($value = null)
    {
        if ($value === null) {
            $value = new ObjectId();
        }

        if (!$value instanceof ObjectId) {
            $value = new ObjectId((string) $value);
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}

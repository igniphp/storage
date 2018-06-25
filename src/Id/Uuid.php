<?php declare(strict_types=1);

namespace Igni\Storage\Id;

use Igni\Storage\Exception\MappingException;
use Igni\Utils\Uuid as UuidUtil;

class Uuid extends GenericId
{
    private $long;

    public function __construct($value = null)
    {
        if ($value === null) {
            $value = UuidUtil::generate();
            $this->long = $value;
            return parent::__construct(UuidUtil::toShort($value));
        }

        $uuid = (string) $value;

        if (!UuidUtil::validate($uuid)) {
            $uuid = UuidUtil::fromShort($uuid);
        }

        if (!UuidUtil::validate($uuid)) {
            throw MappingException::forInvalidUuid($value);
        }

        $this->long = $uuid;
        parent::__construct((string) $value);
    }

    public function getShort(): string
    {
        return $this->getValue();
    }

    public function getLong(): string
    {
        return $this->long;
    }
}

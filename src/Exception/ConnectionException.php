<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Driver\Connection;

class ConnectionException extends DriverException
{
    public static function forExecutionFailure(string $reason, Connection $connection, array $parameters): self
    {
        $connection = get_class($connection);

        return new self("Failed to process ${connection}::execute(${parameters[0]}). Reason: ${reason}");
    }
}

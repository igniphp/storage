<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Driver\Connection;

class ConnectionException extends DriverException
{
    public static function forExecutionFailure(string $reason, Connection $connection, array $parameters): ConnectionException
    {
        $connection = get_class($connection);
        $parameters = implode(',', $parameters);

        return new self("Failed to process ${connection}::execute(${parameters}). Reason: ${reason}");
    }
}

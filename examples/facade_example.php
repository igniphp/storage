<?php declare(strict_types=1);
/**
 * Simple tutorial step by step introducing user to world of storage framework.
 * Each section contains comment explaining what is happening behind the scenes.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Igni\Storage\Mapping\Annotation\Entity;
use Igni\Storage\Mapping\Annotation\Property;
use Igni\Storage\Id\AutoGenerateId;
use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;
use Igni\Storage\Storage;
use Igni\Storage\Mapping\Collection\Collection;
use Igni\Storage\Id\GenericId;
use Igni\Storage\StorageManager;
use Igni\Storage\Storable;

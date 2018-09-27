<?php declare(strict_types=1);

namespace Igni\Storage\Migration;

/**
 * Used by migration manager to store and retrieve target version.
 * Version synchronizer can utilize filesystem or database.
 *
 * @package Igni\Storage\Migration
 */
interface VersionSynchronizer
{
    public function getVersion(): Version;
    public function setVersion(Version $version): void;
}

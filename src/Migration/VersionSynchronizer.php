<?php declare(strict_types=1);

namespace Igni\Storage\Migration;

interface VersionSynchronizer
{
    public function getVersion(): Version;
    public function setVersion(Version $version): void;
}

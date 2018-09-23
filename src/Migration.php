<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Migration\Version;

interface Migration
{
    public function up(): void;
    public function down(): void;
    public function getVersion(): Version;
}

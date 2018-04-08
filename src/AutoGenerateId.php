<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Mapping\Annotations\Types;

trait AutoGenerateId
{
    /**
     * @Types\Id()
     */
    protected $id;

    public function getId(): Id
    {
        if ($this->id === null) {
            $this->id = new Uuid();
        }

        return $this->id;
    }
}

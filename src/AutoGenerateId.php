<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Mapping\Annotations\Types\Id;

trait AutoGenerateId
{
    /**
     * @Id()
     */
    protected $id;

    public function getId(): \Igni\Storage\Id
    {
        if ($this->id === null) {
            $this->id = new Uuid();
        }

        return $this->id;
    }
}

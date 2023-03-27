<?php
declare(strict_types=1);

namespace Sidus\DataGridBundle\Model;

trait AttributesTrait
{
    /** @var array */
    protected $attributes = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function unsetAttribute(string $key): void
    {
        unset($this->attributes[$key]);
    }
}

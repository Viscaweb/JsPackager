<?php

namespace Visca\JsPackager\Configuration;

use Visca\JsPackager\Resource\AssetResource;

/**
 * Class Alias
 */
class Alias
{
    /** @var string */
    protected $name;

    /** @var AssetResource */
    protected $resource;

    /** @var Shim[] */
    protected $shims;

    public function __construct(string $name, AssetResource $resource, array $shims = [])
    {
        $this->name = $name;
        $this->resource = $resource;
        $this->shims = $shims;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResource(): AssetResource
    {
        return $this->resource;
    }

    /**
     * @return Shim[]
     */
    public function getShims(): array
    {
        return $this->shims;
    }
}

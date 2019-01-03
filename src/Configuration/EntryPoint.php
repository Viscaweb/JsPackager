<?php

namespace Visca\JsPackager\Configuration;

use Visca\JsPackager\Resource\AssetResource;
use Visca\JsPackager\Resource\UrlAssetResource;

class EntryPoint
{
    /** @var string */
    protected $name;

    /** @var Resource */
    protected $resource;

    /** @var UrlAssetResource[] */
    protected $externalResources;

    public static function createFromResource(string $name, AssetResource $resource, array $externalResources = [])
    {
        return new self($name, $resource, $externalResources);
    }

    /**
     * EntryPoint constructor.
     *
     * @param string $name
     * @param AssetResource      $resource
     * @param UrlAssetResource[] $externalResources Scripts that will be loaded in addition to the AssetResource. Mainly
     *                                              used to include scripts that are required to be included with extra
     *                                              script tags from external servers.
     */
    public function __construct(string $name, AssetResource $resource, array $externalResources = [])
    {
        $this->name = $name;
        $this->resource = $resource;
        $this->externalResources = $externalResources;
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
     * @return UrlAssetResource[]
     */
    public function getExternalResources(): array
    {
        return $this->externalResources;
    }
}

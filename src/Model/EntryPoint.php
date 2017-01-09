<?php

namespace Visca\JsPackager\Model;

use Visca\JsPackager\Model\Resource;

/**
 * Class EntryPoint
 */
class EntryPoint
{
    /** @var string */
    protected $name;

    /** @var Resource */
    protected $resource;

    /** @var UrlResource[] */
    protected $externalResources;

    /**
     * EntryPoint constructor.
     *
     * @param string        $name
     * @param Resource      $resource
     * @param UrlResource[] $externalResources Scripts that will be loaded in addition to the Resource. Mainly used to include scripts that are required to be included with extra script tags from external servers.
     */
    public function __construct($name, Resource $resource, array $externalResources = [])
    {
        $this->name = $name;
        $this->resource = $resource;
        $this->externalResources = $externalResources;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return UrlResource[]
     */
    public function getExternalResources()
    {
        return $this->externalResources;
    }
}

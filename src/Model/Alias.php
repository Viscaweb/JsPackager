<?php

namespace Visca\JsPackager\Model;

/**
 * Class Alias
 */
class Alias
{
    /** @var string */
    protected $name;

    /** @var Resource */
    protected $resource;

    /** @var Shim[] */
    protected $shims;

    /**
     * Alias constructor.
     *
     * @param string   $name
     * @param Resource $resource
     * @param Shim[]   $shim
     */
    public function __construct($name, Resource $resource, $shims = [])
    {
        $this->name = $name;
        $this->resource = $resource;
        $this->shims = $shims;
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
     * @return Shim[]
     */
    public function getShims()
    {
        return $this->shims;
    }
}

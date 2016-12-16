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

    /**
     * EntryPoint constructor.
     *
     * @param string   $name
     * @param Resource $resource
     */
    public function __construct($name, Resource $resource)
    {
        $this->name = $name;
        $this->resource = $resource;
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
}

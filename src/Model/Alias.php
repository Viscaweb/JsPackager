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

    /**
     * Alias constructor.
     *
     * @param string   $name
     * @param Resource $resource
     */
    public function __construct($name, $resource)
    {
        $this->name = $name;
        $this->resource = $resource;
    }
}

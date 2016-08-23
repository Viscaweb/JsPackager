<?php

namespace Visca\JsPackager\Configuration;

/**
 * Class EntryPoint.
 */
class EntryPointFile implements EntryPointInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * EntryPointFile constructor.
     *
     * @param string $name
     * @param string $path
     */
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return empty($this->path) ? null : '/'.rtrim($this->path, '/');
    }
}

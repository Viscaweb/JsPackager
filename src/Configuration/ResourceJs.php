<?php

namespace Visca\JsPackager\Configuration;

/**
 * Class ResourceJs.
 */
class ResourceJs
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Shim[]
     */
    protected $shims;

    /**
     * @return string
     */
    public function getPath()
    {
        return '/'.ltrim($this->path, '/');
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Shim[]
     */
    public function getShims()
    {
        return $this->shims;
    }

    /**
     * @param Shim[] $shims
     *
     * @return $this
     */
    public function setShims($shims)
    {
        $this->shims = $shims;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }
}

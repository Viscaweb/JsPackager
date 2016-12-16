<?php

namespace Visca\JsPackager\Model;

/**
 * Class Shim.
 */
class Shim
{
    /**
     * @var string
     */
    protected $globalVariable;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @return string
     */
    public function getGlobalVariable()
    {
        return $this->globalVariable;
    }

    /**
     * @param string $globalVariable
     *
     * @return $this
     */
    public function setGlobalVariable($globalVariable)
    {
        $this->globalVariable = $globalVariable;

        return $this;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param string $moduleName
     *
     * @return $this
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }
}

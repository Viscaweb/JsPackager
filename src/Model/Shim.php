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
     * Shim constructor.
     *
     * @param string $globalVariable
     * @param string $moduleName
     */
    public function __construct($globalVariable, $moduleName)
    {
        $this->globalVariable = $globalVariable;
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getGlobalVariable()
    {
        return $this->globalVariable;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}

<?php

namespace Visca\JsPackager\Configuration;

class Shim
{
    /** @var string */
    protected $globalVariable;

    /** @var string */
    protected $moduleName;

    /**
     * Shim constructor.
     *
     * @param string $globalVariable Variable name that should be available that contains the exported
     *                               resource in the $moduleName
     * @param string $moduleName
     */
    public function __construct(string $globalVariable, string $moduleName)
    {
        $this->globalVariable = $globalVariable;
        $this->moduleName = $moduleName;
    }

    public function getGlobalVariable(): string
    {
        return $this->globalVariable;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }
}

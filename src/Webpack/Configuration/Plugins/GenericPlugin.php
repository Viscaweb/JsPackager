<?php

namespace Visca\JsPackager\Webpack\Configuration\Plugins;

class GenericPlugin extends AbstractPluginDescriptor
{
    protected $name;
    protected $moduleName;
    protected $options;

    /**
     * GenericPlugin constructor.
     *
     * @param $name
     * @param $moduleName
     * @param $options
     */
    public function __construct($name, $moduleName, array $options = [])
    {
        $this->name = $name;
        $this->moduleName = $moduleName;
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getRequireCall()
    {
        $moduleName = $this->getModuleName();

        $output = 'var '.
            $this->name().
            ' = require(\''.$moduleName.'\');';

        return $output;
    }

/*
    public function getName()
    {
        return 'duplicatePackageCheckerWebpackPlugin';
    }

    public function getModuleName()
    {
        return 'duplicate-package-checker-webpack-plugin';
    }

    public function getOptions()
    {
        return [];
    }
*/
}

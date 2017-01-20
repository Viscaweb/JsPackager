<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class PluginDescriptorInterface
 */
interface PluginDescriptorInterface
{
    /**
     * Returns the object name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the options to be serialized as JSON
     *
     * @return array
     */
    public function getOptions();

    /**
     * If this plugin requires to include a JS module,
     * this method will return this name.
     *
     * @return string|null
     */
    public function getModuleName();

    /**
     * @return string
     */
    public function getRequireCall();
}

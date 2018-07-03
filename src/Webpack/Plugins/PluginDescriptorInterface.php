<?php

namespace Visca\JsPackager\Webpack\Plugins;

interface PluginDescriptorInterface
{
    /**
     * Returns the object name.
     *
     * @return string
     */
    public function name();

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

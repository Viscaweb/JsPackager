<?php declare(strict_types=1);

namespace Visca\JsPackager\Webpack\Configuration\Plugins;

class DevServerStatsPlugin extends AbstractPluginDescriptor
{
    /**
     * Returns the object name.
     *
     * @return string
     */
    public function name()
    {
        return 'webpackStatsPlugin';
    }

    /**
     * Returns the options to be serialized as JSON
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * If this plugin requires to include a JS module,
     * this method will return this name.
     *
     * @return string|null
     */
    public function getModuleName()
    {
        return './WebpackStatsPlugin';
    }
}
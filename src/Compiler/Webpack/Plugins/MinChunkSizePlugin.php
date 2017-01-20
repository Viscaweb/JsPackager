<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class MinChunkSizePlugin
 */
class MinChunkSizePlugin extends AbstractPluginDescriptor
{
    public function getName()
    {
        return 'webpack.optimize.MinChunkSizePlugin';
    }

    public function getModuleName()
    {
        return 'webpack';
    }

    public function getOptions()
    {
        return ['minChunkSize' => 200000];
    }
}

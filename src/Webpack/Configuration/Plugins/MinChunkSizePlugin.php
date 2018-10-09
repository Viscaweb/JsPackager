<?php

namespace Visca\JsPackager\Webpack\Configuration\Plugins;

class MinChunkSizePlugin extends AbstractPluginDescriptor
{
    public function name()
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

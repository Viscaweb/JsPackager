<?php

namespace Visca\JsPackager\Webpack\Plugins;

class UglifyJsPlugin extends AbstractPluginDescriptor
{
    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return 'webpack';
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return 'webpack.optimize.UglifyJsPlugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [
            'sourceMap' => true
        ];
    }
}

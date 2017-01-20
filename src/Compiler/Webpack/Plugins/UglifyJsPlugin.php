<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class UglifyJsPlugin
 */
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
    public function getName()
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

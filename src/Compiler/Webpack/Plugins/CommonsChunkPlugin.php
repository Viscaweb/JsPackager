<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class CommonsChunkPlugin
 */
class CommonsChunkPlugin extends AbstractPluginDescriptor
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
        return 'webpack.optimize.CommonsChunkPlugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [
            'name' => 'vendor0',
            'filename' => 'commons.[hash].js'
        ];
    }
}

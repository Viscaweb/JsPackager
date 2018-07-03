<?php

namespace Visca\JsPackager\Webpack\Plugins;

class BundleAnalyzerPlugin extends AbstractPluginDescriptor
{
    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return 'webpack-bundle-analyzer';
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return 'webpackBundleAnalyzer.BundleAnalyzerPlugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [
            'analyzerMode' => 'static',
            'reportFilename'=>'report.html',
            'openAnalyzer'=> true,
            'generateStatsFile' => true,
            'statsFilename' => 'stats.json',
            'logLevel' => 'silent'
        ];
    }
}
<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class BundleAnalyzerPlugin
 */
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
    public function getName()
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
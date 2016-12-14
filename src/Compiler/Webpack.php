<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Compiler\Config\WebpackConfig;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\UrlResolver;

/**
 * Class Webpack
 */
class Webpack extends AbstractCompiler
{
    /** @var WebpackConfig */
    protected $webpackConfig;

    /**
     * Webpack constructor.
     *
     * @param WebpackConfig $webpackConfig
     * @param string        $temporalPath
     * @param bool          $debug
     */
    public function __construct(WebpackConfig $webpackConfig, $debug = false)
    {
        $this->webpackConfig = $webpackConfig;
        $this->setDebug($debug);
    }

    /**
     * @param string                  $pageName
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function compile($pageName, ConfigurationDefinition $config)
    {
        $this->compileWebpackConfig($config);

        return $this->compileJs($pageName, $config);
    }

    /**
     * Builds the content of webpack.config.js from a ConfigurationDefinition.
     *
     * @param ConfigurationDefinition $config
     */
    protected function compileWebpackConfig(ConfigurationDefinition $config)
    {
        $webpackConfig = $this->webpackConfig->compile($config);

        $path = $this->getTemporalPath();

        file_put_contents($path.'/webpack.config.js', $webpackConfig);
    }

    /**
     * @return string
     */
    protected function compileJs($pageName, $config)
    {
        $path = $this->getTemporalPath();
        $cmd =
            '/Volumes/Develop/GitRepos/viscaweb/life/'.
            'node_modules/.bin/webpack --json --config '.$path.'/webpack.config.js';
//        $output = shell_exec('webpack --json --config '.$path.'/webpack.config.js');
        $output = [];
        $return_var = [];
        $dd = exec($cmd, $output, $return_var);

        $output = implode('', $output);
        $jsonOutput = json_decode($output, true);
        // Analyze output
//        $jsonOutput = json_decode($output, true);
//        $assets = $this->getAssets($jsonOutput);

        $output = '';
        foreach ($jsonOutput['assetsByChunkName'] as $asset) {
            $output.= $this->addScriptTag('/js/min/'.$asset, $config);
        }
//        foreach ($assets as $url) {
//            $output.= $this->addScriptTag($jsonOutput['publicPath'].'/'.$url);
//        }

        return $output;
    }

    /**
     * @param array $stats
     *
     * @return array
     */
    protected function getAssets($stats)
    {
        $assets = [];

        if (isset($stats['assets'])) {
            foreach ($stats['assets'] as $asset) {
                $assets[] = $asset['name'];
            }
        }

        return $assets;
    }

    private function getTemporalPath()
    {
//        return sys_get_temp_dir();

        return '/Volumes/Develop/GitRepos/viscaweb/life/tmp';
    }
}

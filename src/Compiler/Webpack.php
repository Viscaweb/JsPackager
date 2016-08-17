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

    /** @var string */
    protected $temporalPath;

    /**
     * Webpack constructor.
     *
     * @param WebpackConfig $webpackConfig
     * @param string        $temporalPath
     * @param bool          $debug
     */
    public function __construct(WebpackConfig $webpackConfig, $temporalPath, $debug = false)
    {
        $this->webpackConfig = $webpackConfig;
        $this->temporalPath = rtrim($temporalPath, '/').'/';
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

        return $this->compileJs($pageName);
    }

    /**
     * Builds the content of webpack.config.js from a ConfigurationDefinition.
     *
     * @param ConfigurationDefinition $config
     */
    protected function compileWebpackConfig(ConfigurationDefinition $config)
    {
        $webpackConfig = $this->webpackConfig->compile($config);

        file_put_contents($this->temporalPath.'webpack.config.js', $webpackConfig);
    }

    /**
     * @return string
     */
    protected function compileJs($pageName)
    {
        $path = realpath($this->temporalPath);
        $output = shell_exec('cd '.$path.' && webpack --json');

        // Analyze output
        $jsonOutput = json_decode($output, true);
        $assets = $this->getAssets($jsonOutput);

        $output = '';
        foreach ($assets as $url) {
            $output.= $this->addScriptTag($jsonOutput['publicPath'].'/'.$url);
        }

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
}

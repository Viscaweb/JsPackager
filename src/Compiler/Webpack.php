<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Compiler\Config\WebpackConfig;
use Visca\JsPackager\Compiler\Url\UrlProcessor;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\PackageStats;
use Visca\JsPackager\UrlResolver;

/**
 * Class Webpack
 */
class Webpack extends AbstractCompiler
{
    /** @var WebpackConfig */
    protected $webpackConfig;

    /** @var PackageStats */
    protected $lastStats;

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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'webpack';
    }

    /**
     * {@inheritdoc}
     */
    public function compile($entryPoints, ConfigurationDefinition $config)
    {
        $this->compileWebpackConfig($config);

        return $this->doCompilation($entryPoints, $config);
    }

    /**
     * Builds the content of webpack.config.js from a ConfigurationDefinition.
     *
     * @param ConfigurationDefinition $config
     */
    protected function compileWebpackConfig(ConfigurationDefinition $config)
    {
        $webpackConfigPath = $this->webpackConfig->compile($config);
    }

    /**
     * @param EntryPoint|EntryPoint[] $entryPoints Desired entry points to output.
     * @param ConfigurationDefinition $config      Configuration Definition.
     *
     * @return string
     */
    protected function doCompilation($entryPoints, $config)
    {
        $path = $this->webpackConfig->getTemporalPath();
        $cmd =
            '/Volumes/Develop/GitRepos/viscaweb/life/'.
            'node_modules/.bin/webpack --json --config '.$path.'/webpack.config.js';
//        $output = shell_exec('webpack --json --config '.$path.'/webpack.config.js');
        $output = [];
        $return_var = [];
        $dd = exec($cmd, $output, $return_var);

        // Analyze output
        $output = implode('', $output);
        $jsonOutput = json_decode($output, true);
        $this->processStats($jsonOutput, $config);

        $output = '';
        if (is_array($jsonOutput) && isset($jsonOutput['assetsByChunkName'])) {

            // Check if there is any "commons.js" generated output.
            // Favour loading it as the first asset.
            if (isset($jsonOutput['assetsByChunkName']['commons.js'])) {
                $output .= $this->addScriptTag(
                    $config->getOutputPublicPath().$jsonOutput['assetsByChunkName']['commons.js'],
                    $config
                );

                unset($jsonOutput['assetsByChunkName']['commons.js']);
            }

            // Build desired entrypoints
            if (is_array($entryPoints)) {
                $desiredEntryPoints = array_map(
                    function (EntryPoint $ep) {
                        return $ep->getName();
                    },
                    $entryPoints
                );
            } else {
                $desiredEntryPoints = [$entryPoints->getName()];
            }

            foreach ($jsonOutput['assetsByChunkName'] as $chunkName => $asset) {
                if (in_array($chunkName, $desiredEntryPoints)) {
                    $output .= $this->addScriptTag(
                        $config->getOutputPublicPath().$asset,
                        $config
                    );
                }
            }
        } else {
            throw new \RuntimeException(
                'Could not compile JS with webpack.'
            );
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

    /**
     *
     */
    private function processStats($jsonStats, ConfigurationDefinition $config)
    {
        $assetsBuilt = [];
        if ( isset($jsonStats['assetsByChunkName'])) {
            foreach ($jsonStats['assetsByChunkName'] as $name => $asset) {
                $assetsBuilt[$name] = $config->getOutputPublicPath().$asset;
            }
        }

        $errors = [];
        if (isset($jsonStats['errors']) && count($jsonStats['errors'])) {
            $errors[] = $jsonStats['errors'][0];
        }

        $this->lastStats = new PackageStats(
            $assetsBuilt,
            $errors
        );
    }

    /**
     *
     */
    public function getStats()
    {
        return $this->lastStats;
    }
}

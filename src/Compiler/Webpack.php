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

    public function compile(EntryPoint $entryPoint, ConfigurationDefinition $config)
    {
        throw new \RuntimeException('Webpack does not allow compiling just an entry point.');
    }

    /**
     * {@inheritdoc}
     */
    public function compileCollection(ConfigurationDefinition $config)
    {
        $this->compileWebpackConfig($config);

        return $this->doCompilation($config);
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
     * @param ConfigurationDefinition $config      Configuration Definition.
     *
     * @return string  Returns the <script> tags to output.
     *                 If $entryPoints is an array, it will return a string
     *                 array indexed by entryPoint names with the <script>
     *                 tags result of every one of them.
     */
    protected function doCompilation($config)
    {
        // Clear output path
        $this->clearOutputPath($config);


        $path = $this->webpackConfig->getTemporalPath();
        $cmd =
            '/Volumes/Develop/GitRepos/viscaweb/life/'.
            'node_modules/.bin/webpack --json --config '.$path.'/webpack.config.js';

        $output = [];
        $return_var = [];
        $dd = exec($cmd, $output, $return_var);

        // Analyze output
        $output = implode('', $output);
        $jsonOutput = json_decode($output, true);
        $stats = $this->processStats($jsonOutput, $config);

//        $output = '';

        if (!is_array($jsonOutput) || !isset($jsonOutput['assetsByChunkName'])) {
            throw new \RuntimeException(
                'Could not compile JS with webpack.'
            );
        }

        $entryPoints = $config->getEntryPoints();

        // Check if there is any "commons.js" generated output.
        // Favour loading it as the first asset.
        $vendorAssets = $this->getVendorAssets($jsonOutput);


        $output = [];
        foreach ($entryPoints as $entryPoint) {
            $key = $entryPoint->getName();
            $output[$key] = '';

            // Add external scripts if defined in the EntryPoint.
            $externalAssets = $entryPoint->getExternalResources();
            if (count($externalAssets) > 0) {
                foreach ($externalAssets as $script) {
                    $output[$key] .= $this->addScriptTag($script->getUrl());
                }
            }

            if (count($vendorAssets) > 0) {
                foreach ($vendorAssets as $asset) {
                    $output[$key] .= $this->addScriptTag(
                        $config->getOutputPublicPath().$asset,
                        $config
                    );
                }
            }

            foreach ($stats->getAssetsBuilt() as $chunkName => $asset) {
                if ($chunkName == $key) {
                    $output[$key] .= $this->addScriptTag(
                        $asset,
                        $config
                    );
                }
            }
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
     * @param $stats
     *
     * @return array
     */
    protected function getVendorAssets($stats)
    {
        if (!isset($stats['assetsByChunkName'])) {
            return [];
        }

        $keys = array_keys($stats['assetsByChunkName']);
        $vendorKeys = array_filter(
            $keys,
            function ($item) {
                //vendor;
                return (substr($item, 0, 6) === 'vendor');
            }
        );

        $vendorAssets = [];
        foreach ($vendorKeys as $key) {
            $asset = $stats['assetsByChunkName'][$key];
            if (is_array($asset)) {
                // We may have generated source-maps, webpack groups them by filename.
                $asset = $asset[0];
            }

            $vendorAssets[$key] = $asset;
        }

        ksort($vendorAssets);

        return $vendorAssets;
    }

    /**
     *
     */
    private function processStats($jsonStats, ConfigurationDefinition $config)
    {
        $assetsBuilt = [];
        if (isset($jsonStats['assetsByChunkName'])) {
            foreach ($jsonStats['assetsByChunkName'] as $name => $asset) {

                $path = '';
                if (is_string($asset)) {
                    $path = $asset;
                } elseif (is_array($asset)) {
                    // We may have generated source-maps, paths are grouped.
                    $path = $asset[0];
                }

                $assetsBuilt[$name] = $config->getOutputPublicPath().$path;
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

        return $this->lastStats;
    }

    /**
     * @param ConfigurationDefinition $config
     */
    private function clearOutputPath(ConfigurationDefinition $config)
    {
        $path = $config->getBuildOutputPath();

        $files = glob($path.'/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file);
            } // delete file
        }
    }

    /**
     *
     */
    public function getStats()
    {
        return $this->lastStats;
    }
}

<?php

namespace Visca\JsPackager\Compiler\Webpack;

use Visca\JsPackager\Compiler\Webpack\WebpackConfig;
use Visca\JsPackager\Compiler\Url\UrlProcessor;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\PackageStats;
use Visca\JsPackager\UrlResolver;

final class WebpackCompiler
{
    /** @var string */
    private $rootDir;

    /** @var WebpackConfig */
    private $webpackConfig;

    /** @var string */
    private $nodePath;

    /** @var PackageStats */
    private $lastStats;

    /** @var bool */
    private $debug;

    /** @var UrlProcessor */
    private $urlProcessor;

    /**
     * Webpack constructor.
     *
     * @param WebpackConfig $webpackConfig
     * @param bool          $rootDir
     * @param string        $nodePath
     * @param bool          $debug
     * @param UrlProcessor  $urlProcessor
     */
    public function __construct(
        WebpackConfig $webpackConfig,
        $rootDir,
        $nodePath,
        $debug = false,
        UrlProcessor $urlProcessor = null
    ) {
        $this->webpackConfig = $webpackConfig;
        $this->debug = $debug;
        $this->nodePath = $nodePath;
        $this->rootDir = dirname(rtrim($rootDir, '/'));
        $this->urlProcessor = $urlProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function compileCollection(ConfigurationDefinition $config)
    {
        $this->compileWebpackConfig($config, $this->debug);

        return $this->doCompilation($config);
    }

    /**
     * Builds the content of webpack.config.js from a ConfigurationDefinition.
     *
     * @param ConfigurationDefinition $config
     */
    private function compileWebpackConfig(ConfigurationDefinition $config, $debug = false)
    {
        $webpackConfigPath = $this->webpackConfig->compile($config, $debug);
    }

    /**
     * @param ConfigurationDefinition $config      Configuration Definition.
     *
     * @return string  Returns the <script> tags to output.
     *                 If $entryPoints is an array, it will return a string
     *                 array indexed by entryPoint names with the <script>
     *                 tags result of every one of them.
     */
    private function doCompilation($config)
    {
        // Clear output path
        $this->clearOutputPath($config);

        $path = $this->webpackConfig->getTemporalPath();
        $cmd =
            // Be sure our node_modules folder is available by node
            'export NODE_PATH=\''.$this->rootDir.'/node_modules/\' && '.$this->nodePath.' '.
            $this->rootDir.'/node_modules/.bin/webpack --json --config '.$path.'/webpack.config.'.$config->getName().'.js';

        $output = [];
        $return_var = [];
        $dd = exec($cmd, $output, $return_var);

        // Analyze output
        $stats = $this->processStats($output, $config);

        $entryPoints = $config->getEntryPoints();

        // Check if there is any "commons.js" generated output.
        // Favour loading it as the first asset.
        $vendorAssets = $stats->getVendorAssets();


        $output = [];
        foreach ($entryPoints as $entryPoint) {
            $key = $entryPoint->getName();
            $output[$key] = '';

            // Add external scripts if defined in the EntryPoint.
            $externalAssets = $entryPoint->getExternalResources();
            if (count($externalAssets) > 0) {
                foreach ($externalAssets as $script) {
                    $output[$key] .= $this->addScriptTag($script->getUrl(), $config);
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
    private function getAssets($stats)
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
     * @param string                  $webpackOutput
     * @param ConfigurationDefinition $config
     *
     * @throws \RuntimeException
     */
    private function processStats($webpackOutput, ConfigurationDefinition $config)
    {
        // Try to convert output to JSON
        $webpackOutput = implode('', $webpackOutput);
        $pos = strpos($webpackOutput, '{');
        if ($pos > 0) {
            $webpackOutput = substr($webpackOutput, $pos);
        }

        $pos = strrpos($webpackOutput, '}');
        if ($pos != (strlen($webpackOutput) - 1)) {
            $webpackOutput = substr($webpackOutput, 0, $pos+1);
        }

        $jsonStats = json_decode($webpackOutput, true);
        if ($jsonStats === false) {
            throw new \RuntimeException('Could not json_decode on webpack output.');
        }

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

        $vendorAssets = [];
        $keys = array_keys($jsonStats['assetsByChunkName']);
        $vendorKeys = array_filter(
            $keys,
            function ($item) {
                //vendor;
                return (substr($item, 0, 6) === 'vendor');
            }
        );

        $vendorAssets = [];
        foreach ($vendorKeys as $key) {
            $asset = $jsonStats['assetsByChunkName'][$key];
            if (is_array($asset)) {
                // We may have generated source-maps, webpack groups them by filename.
                $asset = $asset[0];
            }

            $vendorAssets[$key] = $asset;
        }
        ksort($vendorAssets);

        $errors = [];
        if (isset($jsonStats['errors']) && count($jsonStats['errors'])) {
            $errors[] = $jsonStats['errors'][0];
        }

        $this->lastStats = new PackageStats(
            $assetsBuilt,
            $vendorAssets,
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
     * @param string                       $url
     * @param ConfigurationDefinition|null $config
     *
     * @return string
     */
    private function addScriptTag($url, ConfigurationDefinition $config = null)
    {
        if(!is_null($config) && !is_null($this->urlProcessor)){
            $url = $this->urlProcessor->processUrl($url, $config);
        }

        $tag = '<script src="'.$url.'"></script>';

        return $tag;
    }
}

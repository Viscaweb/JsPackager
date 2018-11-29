<?php

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\TemplateEngine\TemplateEngine;
use Visca\JsPackager\Utils\FileSystem;
use Visca\JsPackager\Webpack\Configuration\Loaders\JsonLoader;
use Visca\JsPackager\Webpack\Configuration\Plugins\BundleAnalyzerPlugin;
use Visca\JsPackager\Webpack\Configuration\Plugins\CommonsChunkPlugin;
use Visca\JsPackager\Webpack\Configuration\Plugins\DuplicatePackageCheckerPlugin;
use Visca\JsPackager\Webpack\Configuration\Plugins\GenericPlugin;
use Visca\JsPackager\Webpack\Configuration\Plugins\MinChunkSizePlugin;
use Visca\JsPackager\Webpack\Configuration\Plugins\PluginDescriptorInterface;
use Visca\JsPackager\Webpack\Configuration\Plugins\ProvidePlugin;
use Visca\JsPackager\Webpack\Configuration\Plugins\UglifyJsPlugin;

class WebpackConfigBuilder
{
    /** @var string */
    private $webpackConfigFilePath;

    /** @var string */
    protected $temporalPath;

    /** @var PluginDescriptorInterface[] */
    protected $plugins;

    public function __construct(
        string $webpackConfigFilePath,
        string $temporalPath,
        array $plugins = []
    ) {
        FileSystem::ensureDirExists($temporalPath);
        $this->webpackConfigFilePath = $webpackConfigFilePath;
        $this->temporalPath = realpath($temporalPath);
        $this->plugins = $plugins;
    }

    public function generateConfigurationFile(ConfigurationDefinition $config, string $path): string
    {
        $this->generateEntryPointsFile($config, $path);
        $this->generateResolveAliasesFile($config, $path);

        $webpackConfigPath = $path.'/'.$this->getNamespacedFilename($config, 'webpack.config.js');

        $webpackConfig = file_get_contents($this->webpackConfigFilePath);
        $webpackConfig = $this->generateOutputConfiguration($config, $webpackConfig);

        file_put_contents($webpackConfigPath, $webpackConfig);

        return $webpackConfigPath;
    }

    private function generateOutputConfiguration(ConfigurationDefinition $config, string $webpackConfig): string
    {
        $webpackConfig = str_replace(
            ['%output.publicPath%', '%output.path%'],
            [$config->getOutputPublicPath(), '/'],
            $webpackConfig
        );

        return $webpackConfig;
    }

    /**
     * Generates a entry-point-<app>.js file with the content of all existing
     * entry-point files. This file will be referenced in webpack.config.js file.
     */
    private function generateEntryPointsFile(ConfigurationDefinition $config, string $path)
    {
        $array = [];
        foreach ($config->getEntryPoints() as $entryPoint) {
            $array[$entryPoint->getName()] = $entryPoint->getResource()->getPath();
        }

        $output = 'module.exports = ' . json_encode($array,  JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).';';

        $filename = $this->getNamespacedFilename($config, 'entry-points.js');
        file_put_contents($path.'/'.$filename, $output);
    }

    private function generateResolveAliasesFile(ConfigurationDefinition $config, string $path)
    {
        $array = [];
        foreach ($config->getAlias() as $alias) {
            $array[$alias->getName()] = $alias->getResource()->getPath();
        }

        $output = 'module.exports = '.json_encode($array, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).';';

        $filename = $this->getNamespacedFilename($config, 'alias.js');
        file_put_contents($path.'/'.$filename, $output);
    }

    private function getNamespacedFilename(ConfigurationDefinition $config, string $filename): string
    {
        return /*$config->getName().'/'.*/$filename;
    }

    /**
     * @param PluginDescriptorInterface[] $plugins
     *
     * @return array
     */
    private function getJsModules($plugins)
    {
        $jsModules = [];
        foreach ($plugins as $plugin) {
            $moduleName = $plugin->getModuleName();
            if ($moduleName !== null && !isset($jsModules[$moduleName])) {
                $jsModules[$moduleName] = $plugin->getRequireCall();
            }
        }

        return $jsModules;
    }

    /**
     * @return PluginDescriptorInterface[]
     */
    private function getPlugins(ConfigurationDefinition $config, bool $debug = false): array
    {
        $shimmingModules = $this->getShimsModules($config);

        $plugins = [];
        $plugins[] = new CommonsChunkPlugin($config);
        if ($config->isMinifyEnabled()) {
            $plugins[] = new UglifyJsPlugin();
        }
        $plugins[] = new MinChunkSizePlugin();
//        $plugins[] = new DuplicatePackageCheckerPlugin();
        $plugins[] = new GenericPlugin('webpack2PolyfillPlugin', 'webpack2-polyfill-plugin');
        $plugins[] = new GenericPlugin(
            'webpackStatsPlugin',
            './../../../../../WebpackStatsPlugin',
            ['path' => $this->getTemporalPath().'/'.$config->getName()]
        );

        if ($debug) {
            $plugins[] = new BundleAnalyzerPlugin();
        }

        if (\count($shimmingModules) > 0) {
            $plugins[] = new ProvidePlugin($shimmingModules);
        }

        return $plugins;
    }

    /**
     * @param Alias[] $aliases
     *
     * @return Shim[]
     */
    private function getShimsModules(ConfigurationDefinition $config): array
    {
        $shimmingModules = [];

        foreach ($config->getAlias() as $alias) {
            $shims = $alias->getShims();

            if (count($shims) > 0) {
                foreach ($shims as $shim) {
                    if ($shim instanceof Shim) {
                        $shimmingModules[] = $shim;
                    }
                }
            }
        }

        $shimmingModules[] = new Shim('Promise', 'es6-promise');

        return $shimmingModules;
    }
}

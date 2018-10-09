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
    /** @var TemplateEngine */
    protected $engine;

    /** @var string */
    protected $templatePath;

    /** @var string */
    protected $temporalPath;

    /** @var PluginDescriptorInterface[] */
    protected $plugins;

    public function __construct(
        TemplateEngine $engine,
        string $templatePath,
        ?string $temporalPath = null,
        array $plugins = []
    ) {
        FileSystem::ensureDirExists($temporalPath);

        $this->engine = $engine;
        $this->temporalPath = realpath($temporalPath);
        $this->templatePath = $templatePath;
        $this->plugins = $plugins;
    }

    /**
     * @param ConfigurationDefinition $config Configuration file.
     * @param bool                    $debug  Enables some debugging info in the output.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function generateConfigurationFile(ConfigurationDefinition $config, bool $debug = false)
    {
        $aliases = $config->getAlias();

        //$webpackAlias = $this->getWebpackAliases($aliases);
        //$shimmingModules = $this->getShimsModules($aliases);
        $plugins = $this->getPlugins($config, $debug);
        //$jsModules = $this->getJsModules($plugins);

        $context = new WebpackConfiguration(
            $config->getBuildOutputPath(),
            $config->getEntryPoints(),
            $config->getAlias(),
            $plugins
        );

        $context->setOutputPublicPath($config->getOutputPublicPath());

        $output = $this->engine->render(
            $this->templatePath,
            [
                'outputPath' => $context->outputPath(),
                'outputPublicPath' => $context->outputPublicPath(),
                'entryPoints' => $context->entryPoints(),
                'aliases' => $context->aliases(),
                'plugins' => $context->plugins(),
                'modules' => $context->modules()
            ]
        );

        $path = $this->temporalPath.'/'.$config->getName();
        FileSystem::ensureDirExists($path);
        FileSystem::saveContent(
            $configFileLocation = $path.'/webpack.config.'.$config->getName().'.js',
            $output
        );

        return $configFileLocation;
    }

    public function getTemporalPath(): string
    {
        return $this->temporalPath;
    }

    /**
     * @return array
     */
    private function getLoaders()
    {
        $loaders = [];
        $loaders[] = new JsonLoader();

        return $loaders;
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
     */
    private function getWebpackAliases(array $aliases): array
    {
        $webpackAlias = [];

        foreach ($aliases as $alias) {
            $resource = $alias->getResource();
            $webpackAlias[$alias->getName()] = $resource->getPath();
        }

        return $webpackAlias;
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

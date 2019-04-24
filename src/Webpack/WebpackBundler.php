<?php

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\JavascriptBundler;
use Visca\JsPackager\Report\BundleReport;
use Visca\JsPackager\Shell\NodeJsShellExecutor;
use Visca\JsPackager\Utils\FileSystem;

class WebpackBundler implements JavascriptBundler
{
    const BINARY_WEBPACK = './node_modules/.bin/webpack';

    /** @var WebpackConfigBuilder */
    private $webpackConfigBuilder;

    /** @var NodeJsShellExecutor */
    private $nodeJsShellExecutor;

    /** @var string */
    private $tmpPath;

    /** @var bool */
    private $devMode;

    public function __construct(WebpackConfigBuilder $webpackConfig, NodeJsShellExecutor $nodeJsShellExecutor, string $tmpPath, bool $devMode = true)
    {
        $this->webpackConfigBuilder = $webpackConfig;
        $this->nodeJsShellExecutor = $nodeJsShellExecutor;
        $this->tmpPath = $tmpPath;
        $this->devMode = $devMode;
    }

    public function getName(): string
    {
        return 'webpack';
    }

    public function bundle(ConfigurationDefinition $configuration, string $environment): BundleReport
    {
        $path = $this->tmpPath . '/'.$configuration->getName();
        $this->webpackConfigBuilder->generateConfigurationFile($configuration, $path);

        $webpackConfigPath = $this->webpackConfigBuilder->configurationFilePath($path, $environment);

        return $this->runCompilation($webpackConfigPath, $configuration);
    }

    /**
     * @throws \RuntimeException
     */
    private function runCompilation($webpackConfigFile, ConfigurationDefinition $config): BundleReport
    {
        FileSystem::cleanDir($config->getBuildOutputPath());

        $mode = $this->devMode ? '-d' : '-p';

        $output = $this->nodeJsShellExecutor->run(
            self::BINARY_WEBPACK . ' ' . $mode .' --json --config ' . $webpackConfigFile,
            $config->getProjectRootPath()
        );

        if (!$output) {
            throw new \RuntimeException('Webpack spit some kind of error.');
        }

        // Analyze output
        return BuildReportFactory::create($output);
    }
}

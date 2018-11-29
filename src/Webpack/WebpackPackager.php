<?php

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\JavascriptPackager;
use Visca\JsPackager\Report\PackageReport;
use Visca\JsPackager\Shell\NodeJsShellExecutor;
use Visca\JsPackager\Utils\FileSystem;

class WebpackPackager implements JavascriptPackager
{
    const BINARY_WEBPACK = './node_modules/.bin/webpack';

    /** @var WebpackConfigBuilder */
    private $webpackConfigBuilder;

    /** @var NodeJsShellExecutor */
    private $nodeJsShellExecutor;

    /** @var string */
    private $tmpPath;

    public function __construct(WebpackConfigBuilder $webpackConfig, NodeJsShellExecutor $nodeJsShellExecutor, string $tmpPath)
    {
        $this->webpackConfigBuilder = $webpackConfig;
        $this->nodeJsShellExecutor = $nodeJsShellExecutor;
        $this->tmpPath = $tmpPath;
    }

    public function getName(): string
    {
        return 'webpack';
    }

    public function package(ConfigurationDefinition $configuration): PackageReport
    {
        $path = $this->tmpPath . '/'.$configuration->getName();
        $webpackConfigPath = $this->webpackConfigBuilder->generateConfigurationFile($configuration, $path);

        return $this->runCompilation($webpackConfigPath, $configuration);
    }

    /**
     * @throws \RuntimeException
     */
    private function runCompilation($webpackConfigFile, ConfigurationDefinition $config): PackageReport
    {
        FileSystem::cleanDir($config->getBuildOutputPath());

        $output = $this->nodeJsShellExecutor->run(
            self::BINARY_WEBPACK . ' --json --config ' . $webpackConfigFile,
            $config->getProjectRootPath()
        );

        if (!$output) {
            throw new \RuntimeException('Webpack spit some kind of error.');
        }

        // Analyze output
        return BuildReportFactory::create($output);
    }
}

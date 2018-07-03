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

    public function __construct(WebpackConfigBuilder $webpackConfig, NodeJsShellExecutor $nodeJsShellExecutor)
    {
        $this->webpackConfigBuilder = $webpackConfig;
        $this->nodeJsShellExecutor = $nodeJsShellExecutor;
    }

    public function getName(): string
    {
        return 'webpack';
    }

    public function package(ConfigurationDefinition $configuration): PackageReport
    {
        $webpackConfigPath = $this->webpackConfigBuilder->generateConfigurationFile($configuration, false);

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
            $config->getWorkingPath()
        );

        // Analyze output
        return BuildReportFactory::create($output);
    }
}

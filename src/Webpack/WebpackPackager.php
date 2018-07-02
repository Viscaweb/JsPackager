<?php

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\JavascriptPackager;
use Visca\JsPackager\Packager\Report\Report;
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


    public function package(ConfigurationDefinition $configuration)
    {
        $webpackConfigPath = $this->webpackConfigBuilder->generateConfigurationFile($configuration, false);

        return $this->runCompilation($webpackConfigPath, $configuration);
    }

    /**
     * @throws \RuntimeException
     */
    private function runCompilation($webpackConfigFile, ConfigurationDefinition $config): Report
    {
        FileSystem::cleanDir($config->getBuildOutputPath());

        $output = $this->nodeJsShellExecutor->run(
            $config->getWorkingPath().'/'.self::BINARY_WEBPACK . ' --json --config ' . $webpackConfigFile,
            $config->getWorkingPath()
        );

        // Analyze output
        return BuildReportFactory::create($output);
    }
}

<?php declare(strict_types=1);

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\JavascriptBundler;
use Visca\JsPackager\Report\BundleReport;

class WebpackDevServerWarmer implements JavascriptBundler
{
    /** @var WebpackConfigBuilder */
    private $webpackConfigBuilder;

    /** @var string */
    private $tmpPath;

    public function __construct(WebpackConfigBuilder $webpackConfig, string $tmpPath)
    {
        $this->webpackConfigBuilder = $webpackConfig;
        $this->tmpPath = $tmpPath;
    }

    public function getName(): string
    {
        return 'webpack-warmer';
    }

    public function bundle(ConfigurationDefinition $configuration, string $environment): BundleReport
    {
        $path = $this->tmpPath . '/'.$configuration->getName();
        $this->webpackConfigBuilder->generateConfigurationFile($configuration, $path);

        return new BundleReport([], [], 0, 0);
    }
}
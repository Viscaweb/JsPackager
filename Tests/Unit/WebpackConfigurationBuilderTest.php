<?php declare(strict_types=1);

namespace Unit;

use PHPUnit\Framework\TestCase;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Webpack\WebpackConfigBuilder;

class WebpackConfigurationBuilderTest extends TestCase
{
    /** @test */
    public function entry_points_are_properly_specified()
    {
        $config = $this->basicConfiguration([
            new EntryPoint('home', new FileAssetResource($this->entryPointsPath.'/hello.world.js', '/'))
        ]);

        $webpackConfigPath = $this->builder->generateConfigurationFile($config, $this->tmpPath);

        $this->assertTrue(file_exists($webpackConfigPath));
    }

    /** @var WebpackConfigBuilder */
    private $builder;

    /** @var string */
    private $entryPointsPath;

    /** @var string */
    private $tmpPath;

    protected function setUp()
    {
        parent::setUp();
        $this->tmpPath = \dirname(__DIR__).'/temp';
        $this->entryPointsPath = \dirname(__DIR__).'/entrypoints';

        $this->builder = new WebpackConfigBuilder(
            \dirname(__DIR__).'/fixtures/webpack2/webpack.config.template.js',
            $this->tmpPath
        );
    }

    /**
     * @param EntryPoint[] $entryPoints
     */
    private function basicConfiguration(array $entryPoints = []): ConfigurationDefinition
    {
        $config = new ConfigurationDefinition(
            'desktop',
        '/',
            $this->tmpPath
        );


        foreach ($entryPoints as $entryPoint) {
            $config->addEntryPoint($entryPoint);
        }

        return $config;
    }
}
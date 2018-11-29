<?php

namespace Visca\JsPackager\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Shell\NodeJsShellExecutor;
use Visca\JsPackager\Webpack\WebpackConfigBuilder;
use Visca\JsPackager\Webpack\WebpackPackager;

class WebpackTest extends TestCase
{
    public function testPackageFileHelloWorld()
    {
        $config = new ConfigurationDefinition('desktop', $this->workingPath);
        $config->setBuildOutputPath($this->tempPath);

        $path = $this->fixturesPath.'/src/hello.world.js';
        $config->addEntryPoint(new EntryPoint(
            'home', new FileAssetResource($path, $path)));

        $report = $this->compile($config);

        $this->assertCount(0, $report->getErrors());
        $this->assertEquals('home', $report->getAssets('home')->getId());
    }



    /** @var string */
    protected $fixturesPath;

    /** @var string */
    protected $resourcesPath;

    /** @var string */
    protected $tempPath;

    /** @var string */
    protected $workingPath;

    public function setUp()
    {
        $path = __DIR__;
        $this->workingPath = \dirname($path, 3);
        $this->fixturesPath = $this->workingPath.'/src/Tests/fixtures/webpack2';
        $this->resourcesPath = $this->workingPath.'/resources';
        $this->tempPath = $this->workingPath.'/var/tmp';
    }

    private function compile(ConfigurationDefinition $config)
    {
        $nodeModulesPath = '/node_modules';
        $nodeShellExecuter = new NodeJsShellExecutor('/usr/local/bin/node', $nodeModulesPath);
        $webpackBuilder = $this->webpackBuilder($config);

        return (new WebpackPackager(
            $webpackBuilder,
            $nodeShellExecuter
        ))->package($config);
    }

    private function webpackBuilder(ConfigurationDefinition $config)
    {
        return new WebpackConfigBuilder(
//            '/web',
            $this->resourcesPath.'/webpack.config.v2.mustache',
            $this->tempPath
        );
    }
}

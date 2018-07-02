<?php

namespace Visca\JsPackager\Tests\Unit;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Shell\NodeJsShellExecutor;
use Visca\JsPackager\TemplateEngine\MustacheEngine;
use Visca\JsPackager\Webpack\WebpackConfigBuilder;
use Visca\JsPackager\Webpack\WebpackPackager;

class WebpackTest extends \PHPUnit_Framework_TestCase
{
    public function testPackageFileHelloWorld()
    {
        $config = new ConfigurationDefinition('desktop', 'prod', $this->workingPath);
        $config->setBuildOutputPath($this->tempPath);
        $config->addEntryPoint(new EntryPoint('home', new FileAssetResource($this->fixturesPath.'/src/hello.world.js')));

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
        $this->resourcesPath = $this->workingPath.'/src/resources';
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
        $path = __DIR__;
        $engine = new MustacheEngine(new \Mustache_Engine());
/*
        $config = new ConfigurationDefinition('desktop', 'prod', \dirname($path, 2));
        $config->setOutputPublicPath('');
        $config->setBuildOutputPath('');
*/
        return new WebpackConfigBuilder(
            $engine,
//            '/web',
            $this->resourcesPath.'/webpack.config.v2.mustache',
            $this->tempPath
        );
    }
}

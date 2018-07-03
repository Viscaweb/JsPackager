<?php

namespace Visca\JsPackager\Tests\Unit;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Resource\StringAssetResource;
use Visca\JsPackager\TemplateEngine\MustacheEngine;
use Visca\JsPackager\TemplateEngine\TemplateEngine;
use Visca\JsPackager\Webpack\WebpackConfigBuilder;

class WebpackConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var TemplateEngine */
    protected $engine;

    /** @var WebpackConfigBuilder */
    protected $webpackConfigBuilder;

    /** @var string */
    protected $resourcesPath;

    /** @var string */
    protected $workingPath;

    /** @var ConfigurationDefinition */
    protected $config;

    /** @var string */
    protected $fixturesPath;

    /** @var string */
    protected $tempPath;

    public function setUp()
    {
        parent::setUp();

        $path = __DIR__;
        $this->engine = new MustacheEngine(new \Mustache_Engine());

        $this->fixturesPath = \dirname($path, 2).'/Tests/fixtures/webpack2';
        $this->resourcesPath = \dirname($path, 2).'/resources';
        $this->workingPath = \dirname($path, 2);
        $this->tempPath = \dirname($path, 3).'/var/tmp';

        $this->config = new ConfigurationDefinition('desktop', 'prod', $this->workingPath);
        $this->config->setOutputPublicPath('');
        $this->config->setBuildOutputPath('');

        $this->webpackConfigBuilder = new WebpackConfigBuilder(
            $this->engine,
//            '/web',
            realpath($this->resourcesPath.'/webpack.config.v2.mustache'),
            $this->tempPath
        );
    }

    /**
     * @test Tests an empty config
     */
    public function testEmptyConfig()
    {
        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/emptyConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of output.path
     */
    public function testOutputPath()
    {
        $this->config->setOutputPublicPath('js');

        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/outputPathConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of resolve.alias
     */
    public function testResolveAlias()
    {
        $jquery = new Alias('jquery', new FileAssetResource('js/vendor/jquery.min.js'));

        $this->config->addAlias($jquery);

        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/resolveAliasConfig.js');

        $expected = str_replace(
            '%rootPath%',
            $this->resourcesPath,
            $expected
        );

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Test proper rendering of resolve.alias having
     *       configured shim.
     */
    public function testResolveAliasWithShim()
    {
        $resource = new FileAssetResource('js/vendor/jquery.min.js');
        $jquery = new Alias('jquery', $resource);
        $this->config->addAlias($jquery);

        $shim = new Shim('$', 'jquery');

        $resource = new FileAssetResource('js/vendor/bootstrap.min.js');
        $bootstrap = new Alias('bootstrap', $resource, [$shim]);
        $this->config->addAlias($bootstrap);

        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/resolveAliasWithShimConfig.js');

        $expected = str_replace(
            '%rootPath%',
            $this->resourcesPath,
            $expected
        );

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests `entry`
     */
    public function testEntryPointFromUrlConfig()
    {
        $entryPoint = new EntryPoint('matchPage', new FileAssetResource($this->resourcesPath.'/fixtures/match.page.js'));
        $this->config->addEntryPoint($entryPoint);

        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/entryPointConfig.js');

        $expected = str_replace(
            '%outputPath%',
            $this->resourcesPath,
            $expected
        );

        $this->assertEquals($expected, $output);
    }

    public function testEntryPointFromContent()
    {
        $id = 'matchPage';
        $entryPoint = new EntryPoint(
            $id,
            new StringAssetResource($id,'console.log(\'hello\');', $this->tempPath.'/hello.js')
        );

        $this->config->addEntryPoint($entryPoint);

        $result = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $this->assertNotEmpty($result);
    }
}

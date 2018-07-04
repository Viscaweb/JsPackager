<?php

namespace Visca\JsPackager\Tests\Unit;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\Resource\AliasAssetResource;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Resource\FileOnDemandAssetResource;
use Visca\JsPackager\TemplateEngine\MustacheEngine;
use Visca\JsPackager\TemplateEngine\PHPEngine;
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
//        $this->engine = new MustacheEngine(new \Mustache_Engine());
        $this->engine = new PHPEngine();

        $this->fixturesPath = \dirname($path, 2).'/Tests/fixtures/webpack2';
        $this->resourcesPath = \dirname($path, 3).'/resources';
        $this->workingPath = \dirname($path, 2);
        $this->tempPath = \dirname($path, 3).'/var/tmp';

        $this->config = new ConfigurationDefinition('desktop', 'prod', $this->workingPath, $this->workingPath);
        $this->config->setOutputPublicPath('');
        $this->config->setBuildOutputPath('');

        $this->webpackConfigBuilder = new WebpackConfigBuilder(
            $this->engine,
            realpath($this->resourcesPath.'/webpack.config.v2.php'),
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
        $path = $this->resourcesPath.'/js/vendor/jquery.min.js';
        $jquery = new Alias('jquery', new FileAssetResource($path, $path));

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
        $path = $this->resourcesPath.'/js/vendor/jquery.min.js';
        $resource = new FileAssetResource($path, $path);
        $jquery = new Alias('jquery', $resource);
        $this->config->addAlias($jquery);

        $shim = new Shim('$', 'jquery');

        $path = $this->resourcesPath.'/js/vendor/bootstrap.min.js';
        $resource = new FileAssetResource($path, $path);
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
        $path = $this->resourcesPath.'/fixtures/match.page.js';
        $entryPoint = new EntryPoint('matchPage', new FileAssetResource($path, $path));
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

    public function testEntryPointWithAliasCollection()
    {
        $aliasResource = new AliasAssetResource(['jquery', 'bootstrap', 'socket.io']);
        $entryPoint = new EntryPoint('vendor0-desktop', $aliasResource);
        $this->config->addEntryPoint($entryPoint);

        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/entryPointAliasedConfig.js');

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
            new FileOnDemandAssetResource($id,'console.log(\'hello\');', $this->tempPath)
        );

        $this->config->addEntryPoint($entryPoint);

        $outputPath = $this->webpackConfigBuilder->generateConfigurationFile($this->config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents($this->fixturesPath.'/entryPointFromContentConfig.js');

        $expected = str_replace(
            '%outputPath%',
            realpath($this->workingPath.'/../'),
            $expected
        );
        $this->assertEquals($expected, $output);
    }
}

<?php

namespace Visca\JsPackager\Tests\Functional;

use Visca\JsPackager\Compiler\Webpack\WebpackConfig;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\Shim;
use Visca\JsPackager\Model\Alias;
use Visca\JsPackager\Model\FileResource;
use Visca\JsPackager\Model\StringResource;
use Visca\JsPackager\ConfigurationDefinition;

class WebpackConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConfig()
    {
        $this->assertJavascriptEquals('webpackExpected/emptyConfig.js', $this->config);
    }

    public function testOutputPath()
    {
        $config = clone $this->config;
        $config->setOutputPublicPath('js');

        $this->assertJavascriptEquals('webpackExpected/outputPathConfig.js', $config);
    }

    /**
     * @test Tests proper rendering of resolve.alias
     */
    public function testResolveAlias()
    {
        $jquery = new Alias('jquery', new FileResource('js/vendor/jquery.min.js'));

        $config = clone $this->config;
        $config->addAlias($jquery);

        $this->assertJavascriptEquals('webpackExpected/resolveAliasConfig.js', $config);
    }

    /**
     * @test Test proper rendering of resolve.alias having
     *       configured shim.
     */
    public function testResolveAliasWithShim()
    {
        $jquery = new Alias('jquery', new FileResource('js/vendor/jquery.min.js'));

        $config = clone $this->config;
        $config->addAlias($jquery);

        $shim = new Shim('$', 'jquery');

        $resource = new FileResource('js/vendor/bootstrap.min.js');
        $bootstrap = new Alias('bootstrap', $resource, [$shim]);
        $config->addAlias($bootstrap);

        $this->assertJavascriptEquals('webpackExpected/resolveAliasWithShimConfig.js', $config);
    }

    /**
     * @test Tests `entry`
     */
    public function testEntryPointFromUrlConfig()
    {
        $entryPoint = new EntryPoint('matchPage', new FileResource($this->resourcesPath.'/fixtures/match.page.js'));

        $config = clone $this->config;
        $config->addEntryPoint($entryPoint);

        $this->assertJavascriptEquals('webpackExpected/entryPointConfig.js', $config);
    }

    public function testEntryPointFromContent()
    {
        $entryPoint = new EntryPoint('matchPage', new StringResource('console.log(\'hello\');'));

        $config = clone $this->config;
        $config->addEntryPoint($entryPoint);

        $outputPath = $this->webpackConfig->compile($config);

        $this->assertJavascriptEquals('webpackExpected/entryPointFromContentConfig.js', $config);
    }

    /**
     * @param string                  $expectedJsFile
     * @param ConfigurationDefinition $config
     */
    private function assertJavascriptEquals(
        $expectedJsFile,
        ConfigurationDefinition $config
    ) {
        $expectedJs = file_get_contents(__DIR__ . '/' . $expectedJsFile);
        $expectedJs = str_replace('%rootPath%', $this->resourcesPath, $expectedJs);
        $expectedJs = str_replace('%outputPath%', $this->webpackConfig->getTemporalPath(), $expectedJs);

        $compiledJsFile = $this->webpackConfig->compile($config);
        $compiledJs = file_get_contents($compiledJsFile);

        $this->assertEquals($expectedJs, $compiledJs);
    }

    /** @var \Twig_Environment */
    private $twig;

    /** @var WebpackConfig */
    private $webpackConfig;

    /** @var string */
    private $resourcesPath;

    /** @var string */
    private $rootPath;

    /** @var ConfigurationDefinition */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $path = __DIR__;

        $this->resourcesPath = realpath($path.'/../../Resources');
        $this->rootPath = realpath($path.'/../../Resources/tmp');
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->resourcesPath));

        $this->webpackConfig = new WebpackConfig(
            $this->twig,
            $this->rootPath,
            'webpack.config.yml.dist'
        );
        $this->config = new ConfigurationDefinition('desktop', 'prod');
    }
}
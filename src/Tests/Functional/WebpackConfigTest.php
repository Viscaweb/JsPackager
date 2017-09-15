<?php

namespace Visca\JsPackager\Tests\Functional;

//use Assetic\Factory\Resource\FileResource;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Visca\JsPackager\Compiler\Webpack\WebpackConfig;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Configuration\ResourceJs;
use Visca\JsPackager\Model\Shim;
use Visca\JsPackager\Model\Alias;
use Visca\JsPackager\Model\FileResource;
use Visca\JsPackager\Model\StringResource;
use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class WebpackConfigTest
 */
class WebpackConfigTest extends WebTestCase
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var WebpackConfig */
    protected $webpackConfig;

    /** @var string */
    protected $resourcesPath;

    /** @var string */
    protected $rootPath;

    public function setUp()
    {
        parent::setUp();

        $path = __DIR__;
        $this->twig = $this->getContainer()->get('twig');

        $this->resourcesPath = realpath($path.'/../../Resources');
        $this->rootPath = realpath($path.'/../../Resources/tmp');

        $this->webpackConfig = new WebpackConfig(
            $this->twig,
            $this->rootPath,
            realpath($this->resourcesPath.'/webpack.config.yml.dist')
        );
    }

    /**
     * @test Tests an empty config
     */
    public function testEmptyConfig()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');

        $outputPath = $this->webpackConfig->compile($config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents(__DIR__.'/webpackExpected/emptyConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of output.path
     */
    public function testOutputPath()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');
        $config->setOutputPublicPath('js');

        $outputPath = $this->webpackConfig->compile($config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents(__DIR__.'/webpackExpected/outputPathConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of resolve.alias
     */
    public function testResolveAlias()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');

        $jquery = new Alias('jquery', new FileResource('js/vendor/jquery.min.js'));

        $config->addAlias($jquery);

        $outputPath = $this->webpackConfig->compile($config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents(__DIR__.'/webpackExpected/resolveAliasConfig.js');

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
        $config = new ConfigurationDefinition('desktop', 'prod');

        $resource = new FileResource('js/vendor/jquery.min.js');
        $jquery = new Alias('jquery', $resource);
        $config->addAlias($jquery);

        $shim = new Shim();
        $shim->setGlobalVariable('$')
            ->setModuleName('jquery');

        $resource = new FileResource('js/vendor/bootstrap.min.js');
        $bootstrap = new Alias('bootstrap', $resource, [$shim]);
        $config->addAlias($bootstrap);

        $outputPath = $this->webpackConfig->compile($config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents(__DIR__.'/webpackExpected/resolveAliasWithShimConfig.js');

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
        $entryPoint = new EntryPoint('matchPage', new FileResource($this->resourcesPath.'/fixtures/match.page.js'));
        $config = new ConfigurationDefinition('desktop', 'prod');
        $config->addEntryPoint($entryPoint);

        $outputPath = $this->webpackConfig->compile($config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents(__DIR__.'/webpackExpected/entryPointConfig.js');

        $expected = str_replace(
            '%outputPath%',
            $this->webpackConfig->getTemporalPath(),
            $expected
        );

        $this->assertEquals($expected, $output);
    }

    public function testEntryPointFromContent()
    {
        $entryPoint = new EntryPoint('matchPage', new StringResource('console.log(\'hello\');'));

        $config = new ConfigurationDefinition('desktop', 'prod');
        $config->addEntryPoint($entryPoint);

        $outputPath = $this->webpackConfig->compile($config);
        $output = file_get_contents($outputPath);
        $expected = file_get_contents(__DIR__.'/webpackExpected/entryPointFromContentConfig.js');

        $expected = str_replace(
            '%outputPath%',
            $this->webpackConfig->getTemporalPath(),
            $expected
        );

        $this->assertEquals($expected, $output);
    }
}
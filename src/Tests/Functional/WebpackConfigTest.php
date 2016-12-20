<?php

namespace Visca\JsPackager\Tests\Functional;

//use Assetic\Factory\Resource\FileResource;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Visca\JsPackager\Compiler\Config\WebpackConfig;
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
    protected $tempPath;

    /** @var string */
    protected $resourcesPath;

    public function setUp()
    {
        parent::setUp();

        $this->resourcesPath = './../../Resources/';
        $this->tempPath = $this->resourcesPath.'temp/';
        $this->twig = $this->getContainer()->get('twig');

        $path = __DIR__.'/../../Resources/webpack.config.yml.dist';
        $template = realpath($path);
        $this->webpackConfig = new WebpackConfig('life/', $this->twig, $template, $this->tempPath);
    }

    /**
     * @test Tests an empty config
     */
    public function testEmptyConfig()
    {
        $config = new ConfigurationDefinition('desktop');

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/emptyConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of output.path
     */
    public function testOutputPath()
    {
        $config = new ConfigurationDefinition('desktop');
        $config->setOutputPublicPath('js');

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/outputPathConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of resolve.alias
     */
    public function testResolveAlias()
    {
        $config = new ConfigurationDefinition('desktop');

        $jquery = new Alias('jquery', new FileResource('js/vendor/jquery.min.js'));

        $config->addAlias($jquery);

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/resolveAliasConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Test proper rendering of resolve.alias having
     *       configured shim.
     */
    public function testResolveAliasWithShim()
    {
        $config = new ConfigurationDefinition('desktop');

        $resource = new FileResource('js/vendor/jquery.min.js');
        $jquery = new Alias('jquery', $resource);
        $config->addAlias($jquery);

        $shim = new Shim();
        $shim->setGlobalVariable('$')
            ->setModuleName('jquery');

        $resource = new FileResource('js/vendor/bootstrap.min.js');
        $bootstrap = new Alias('bootstrap', $resource, [$shim]);
        $config->addAlias($bootstrap);

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/resolveAliasWithShimConfig.js');



        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests `entry`
     */
    public function testEntryPointFromUrlConfig()
    {
        $entryPoint = new EntryPoint('matchPage', new FileResource($this->resourcesPath.'fixtures/match.page.js'));
        $config = new ConfigurationDefinition('desktop');
        $config->addEntryPoint($entryPoint);

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/entryPointConfig.js');

        $expected = str_replace('<apath>', $this->webpackConfig->getTemporalPath().'/', $expected);

        $this->assertEquals($expected, $output);
    }

    public function testEntryPointFromContent()
    {
        $entryPoint = new EntryPoint('matchPage', new StringResource('console.log(\'hello\');'));

        $config = new ConfigurationDefinition('desktop');
        $config->addEntryPoint($entryPoint);

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/entryPointFromContentConfig.js');

        $expected = str_replace('<apath>', $this->webpackConfig->getTemporalPath().'/', $expected);

        $this->assertEquals($expected, $output);
    }
}
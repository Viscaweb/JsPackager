<?php

namespace Visca\JsPackager\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Visca\JsPackager\Compiler\Config\WebpackConfig;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\ResourceJs;
use Visca\JsPackager\Configuration\Shim;
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

    public function setUp()
    {
        parent::setUp();

        $this->tempPath = './../../Resources/temp/';
        $this->twig = $this->getContainer()->get('twig');

        $template = realpath(__DIR__.'/../../Resources/webpack.config.yml.dist');
        $this->webpackConfig = new WebpackConfig($this->twig, $template, $this->tempPath);
    }

    /**
     * @test Tests an empty config
     */
    public function testEmptyConfig()
    {
        $config = new ConfigurationDefinition();


        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/emptyConfig.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests proper rendering of output.path
     */
    public function testOutputPath()
    {
        $config = new ConfigurationDefinition();
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
        $config = new ConfigurationDefinition();

        $jquery = new ResourceJs();
        $jquery
            ->setAlias('jquery')
            ->setPath('js/vendor/jquery.min.js');

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
        $config = new ConfigurationDefinition();

        $jquery = new ResourceJs();
        $jquery
            ->setAlias('jquery')
            ->setPath('js/vendor/jquery.min.js');
        $config->addAlias($jquery);

        $shim = new Shim();
        $shim->setGlobalVariable('$')
            ->setModuleName('jquery');

        $bootstrap = new ResourceJs();
        $bootstrap
            ->setAlias('bootstrap')
            ->setPath('js/vendor/bootstrap.min.js')
            ->setShims([$shim]);
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
        $entryPoint = new EntryPoint();
        $entryPoint
            ->setName('matchPage')
            ->setPath('js/pages/match.page.js');

        $config = new ConfigurationDefinition();
        $config->addEntryPoint($entryPoint);

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/entryPointConfig.js');

        $this->assertEquals($expected, $output);
    }

    public function testEntryPointFromContent()
    {
        $entryPoint = new EntryPoint();
        $entryPoint
            ->setName('matchPage')
            ->setContent('console.log(\'hello\');');

        $config = new ConfigurationDefinition();
        $config->addEntryPoint($entryPoint);

        $output = $this->webpackConfig->compile($config);
        $expected = file_get_contents(__DIR__.'/webpackExpected/entryPointFromContentConfig.js');

        $this->assertEquals($expected, $output);
    }
}
<?php

namespace Visca\JsPackager\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Visca\JsPackager\Compiler\Config\WebpackConfig;
use Visca\JsPackager\Compiler\Webpack;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\StringResource;
use Visca\JsPackager\UrlResolver;

/**
 * Class WebpackTest
 */
class WebpackTest extends WebTestCase
{
    /** @var string */
    protected $temporalPath;

    /** @var UrlResolver */
    protected $urlResolver;

    /** @var WebpackConfig */
    private $webpackConfig;

    /** @var Webpack */
    private $compiler;

    /** @var ConfigurationDefinition */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $path = __DIR__;
        $this->twig = $this->getContainer()->get('twig');

        $this->resourcesPath = realpath($path.'/../../Resources');
        $this->rootPath = realpath($path.'/../../Resources/tmp');

        $this->temporalPath = __DIR__.'/../../Resources/temp';
        $template = realpath(__DIR__.'/../../Resources/webpack.config.yml.dist');
        /** @var Twig_Environment $twig */
        $twig = $this->getContainer()->get('twig');
        $this->urlResolver = new UrlResolver($twig);

        $this->webpackConfig = new WebpackConfig(
            $twig,
            $this->rootPath,
            $template
        );
        $this->compiler = new Webpack(
            $this->webpackConfig
        );

        $this->config = new ConfigurationDefinition('desktop');
        $this->config->setBuildOutputPath($this->temporalPath.'/build');
        $this->config->setOutputPublicPath('/js/min/');
    }

    /**
     * @test Test output of an empty Config.
     */
    public function testEmptyConfig()
    {
        $output = $this->compiler->compileCollection($this->config);

        $this->assertCount(0, $output);
    }

    /**
     *
     */
    public function testContentEntryPoint()
    {
        $entryPoint = new EntryPoint('match', new StringResource('console.log(\'hello match\');'));

        $this->config->addEntryPoint($entryPoint);

        $output = $this->compiler->compileCollection($this->config);

        $this->assertEquals(
            '<script src="/js/min/match.dist.js"></script>',
            $output['match']
        );
    }

    /**
     * Tests that defining multiple entry points.
     */
    public function testMultipleEntryPoints()
    {
        $epHome = new EntryPoint(
            'home',
            new StringResource('console.log(\'hello home\');')
        );

        $epContact = new EntryPoint(
            'contact',
            new StringResource('console.log(\'hello contact\');')
        );

        $this->config->addEntryPoint($epHome);
        $this->config->addEntryPoint($epContact);

        $output = $this->compiler->compileCollection($this->config);

        $this->assertEquals('<script src="/js/min/home.dist.js"></script>', $output['home']);
        $this->assertEquals('<script src="/js/min/contact.dist.js"></script>', $output['contact']);
    }
}

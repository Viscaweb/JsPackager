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

        $this->temporalPath = __DIR__.'/../../Resources/temp';
        $template = realpath(__DIR__.'/../../Resources/webpack.config.yml.dist');
        /** @var Twig_Environment $twig */
        $twig = $this->getContainer()->get('twig');
        $this->urlResolver = new UrlResolver($twig);

        $this->webpackConfig = new WebpackConfig('life', $twig, $template, $this->temporalPath);
        $this->compiler = new Webpack(
            $this->webpackConfig,
            $this->temporalPath,
            $this->urlResolver
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
        $output = $this->compiler->compile(
            new EntryPoint('null', new StringResource('')),
            $this->config
        );

        $this->assertEquals('<script src="/js/min/commons.js"></script>', $output);
    }

    /**
     *
     */
    public function testContentEntryPoint()
    {
        $entryPoint = new EntryPoint('match', new StringResource('console.log(\'hello match\');'));

        $this->config->addEntryPoint($entryPoint);

        $output = $this->compiler->compile($entryPoint, $this->config);

        $this->assertEquals(
            '<script src="/js/min/commons.js"></script>'.
            '<script src="/js/min/match.dist.js"></script>',
            $output
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

        $output = $this->compiler->compile($epHome, $this->config);

        $expected =
            '<script src="/js/min/commons.js"></script>'.
            '<script src="/js/min/home.dist.js"></script>';

        $this->assertEquals($expected, $output);
    }
}

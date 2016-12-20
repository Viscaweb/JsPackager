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
    }

    /**
     * @test Test output of an empty Config.
     */
    public function testEmptyConfig()
    {
        $config = new ConfigurationDefinition('desktop');
        $config->setBuildOutputPath($this->temporalPath.'/build');
        $config->setOutputPublicPath('/js/min/');

        $output = $this->compiler->compile('match', $config);

        $this->assertEquals('<script src="/js/min/commons.js"></script>', $output);
    }

    /**
     *
     */
    public function testContentEntryPoint()
    {
        $entryPoint = new EntryPoint('match', new StringResource('console.log(\'hello match\');'));

        $config = new ConfigurationDefinition('desktop');
        $config->setOutputPublicPath('/js/min');
        $config->setBuildOutputPath($this->temporalPath.'/build');
        $config->addEntryPoint($entryPoint);

        $output = $this->compiler->compile($entryPoint, $config);

        $this->assertEquals(
            '<script src="/js/min/commons.js"></script>'.
            '<script src="/js/min/match.dist.js"></script>',
            $output
        );
    }
}

<?php

namespace Visca\JsPackager\Tests;

use Doctrine\Common\Cache\VoidCache;
use Visca\JsPackager\Compiler\Url\UrlProcessor;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\Alias;
use Visca\JsPackager\Model\FileResource;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Compiler\RequireJS;
use Visca\JsPackager\Model\Shim;
use Visca\JsPackager\Model\StringResource;

class RequireJsTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConfig()
    {
        $this->assertJavascriptEquals('requireJsExpected/emptyConfig.js', $this->config);
    }

    public function testBaseUrl()
    {
        $config = clone $this->config;
        $config->setOutputPublicPath('/web/');

        $this->assertJavascriptEquals('requireJsExpected/baseUrl.js', $config);
    }

    public function testAlias()
    {
        $jquery = new Alias('jquery', new FileResource('js/jquery.min.js'));

        $config = clone $this->config;
        $config->addAlias($jquery);

        $this->assertJavascriptEquals('requireJsExpected/alias.js', $config);
    }

    public function testShim()
    {
        $shim = new Shim('$', 'jquery');
        $bootstrap = new Alias('bootstrap', new FileResource('js/bootstrap.min.js'), [$shim]);

        $config = clone $this->config;
        $config->addAlias($bootstrap);

        $this->assertJavascriptEquals('requireJsExpected/shim.js', $config);
    }

    public function testEntryPoint()
    {
        $resource = new StringResource('console.log(\'hello\');');
        $entryPoint = new EntryPoint('xxx', $resource);

        $config = clone $this->config;
        $config->addEntryPoint($entryPoint);

        $this->assertJavascriptEquals('requireJsExpected/entryPoints.js', $config, $entryPoint);
    }

    /**
     * @param string                  $expectedJsFile
     * @param ConfigurationDefinition $config
     * @param EntryPoint|null         $entryPoint
     */
    private function assertJavascriptEquals(
        $expectedJsFile,
        ConfigurationDefinition $config,
        EntryPoint $entryPoint = null
    ) {
        $entryPoint = $entryPoint ?: new EntryPoint('xxx', new StringResource(''));

        $expectedJs = file_get_contents(__DIR__ . '/' . $expectedJsFile);
        $compiledJs = $this->compiler->compile($entryPoint, $config);

        $this->assertEquals($expectedJs, $compiledJs);
    }

    /** @var RequireJS */
    private $compiler;

    /** @var ConfigurationDefinition */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $urlProcessor = new UrlProcessor(new VoidCache(), '');
        $urlProcessor->setCacheBustingEnabled(false);
        $this->compiler = new RequireJS($urlProcessor);

        $this->config = new ConfigurationDefinition('desktop', 'prod');
    }
}

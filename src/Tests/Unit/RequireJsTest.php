<?php

namespace Visca\JsPackager\Tests;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\RequireJS\RequireJSLoader;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Resource\StringAssetResource;
use Visca\JsPackager\Compiler\Url\UrlProcessor;

class RequireJsTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConfig()
    {
        $this->assertJavascriptEquals('emptyConfig.js', $this->config);
    }

    public function testBaseUrl()
    {
        $config = clone $this->config;
        $config->setOutputPublicPath('/web/');

        $this->assertJavascriptEquals('baseUrl.js', $config);
    }

    public function testAlias()
    {
        $jquery = new Alias('jquery', new FileAssetResource('js/jquery.min.js'));

        $config = clone $this->config;
        $config->addAlias($jquery);

        $this->assertJavascriptEquals('alias.js', $config);
    }

    public function testShim()
    {
        $shim = new Shim('$', 'jquery');
        $bootstrap = new Alias('bootstrap', new FileAssetResource('js/bootstrap.min.js'), [$shim]);

        $config = clone $this->config;
        $config->addAlias($bootstrap);

        $this->assertJavascriptEquals('shim.js', $config);
    }

    public function testEntryPoint()
    {
        $resource = new StringAssetResource('console.log(\'hello\');');
        $entryPoint = new EntryPoint('xxx', $resource);

        $config = clone $this->config;
        $config->addEntryPoint($entryPoint);

        $this->assertJavascriptEquals('entryPoints.js', $config, $entryPoint);
    }

    public function testUrlCacheBust()
    {
        $this->markTestSkipped();
        $jquery = new Alias('jquery', new FileAssetResource('js/jquery.min.js'));

        $config = clone $this->config;
        $config->addAlias($jquery);
        $this->urlProcessor->setCacheBustingEnabled(true);

        $this->assertJavascriptEquals('cacheBusting.js', $config);
    }

    /**
     */
    private function assertJavascriptEquals(
        string $expectedJsFile,
        ConfigurationDefinition $config,
        EntryPoint $entryPoint = null
    ) {
        $entryPoint = $entryPoint ?: new EntryPoint('xxx', new StringAssetResource(''));

        $rootPath = \dirname(__DIR__, 2);
        $expectedJs = file_get_contents($rootPath. '/Tests/fixtures/requirejs/' . $expectedJsFile);

        $loader = new RequireJSLoader();
        $compiledJs = $loader->getPageJavascript($entryPoint, $config);

        $this->assertEquals(trim($expectedJs), trim($compiledJs));
    }

    /** @var ConfigurationDefinition */
    private $config;

    /** @var UrlProcessor */
    protected $urlProcessor;

    public function setUp()
    {
        parent::setUp();

//        $this->urlProcessor = new UrlProcessor(new ArrayCache(), '');
//        $this->urlProcessor->setCacheBustingEnabled(false);

        $this->config = new ConfigurationDefinition('desktop', 'prod', \dirname(__DIR__, 2));
    }
}

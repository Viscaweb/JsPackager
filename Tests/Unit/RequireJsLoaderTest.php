<?php

namespace Visca\JsPackager\Tests;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\RequireJS\RequireJSLoader;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Resource\FileOnDemandAssetResource;

class RequireJsLoaderTest extends \PHPUnit_Framework_TestCase
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
        $url = '/js/jquery.min.js';
        $jquery = new Alias('jquery', new FileAssetResource($url, $url));

        $config = clone $this->config;
        $config->addAlias($jquery);

        $this->assertJavascriptEquals('alias.js', $config);
    }

    public function testShim()
    {
        $shim = new Shim('$', 'jquery');
        $url = '/js/bootstrap.min.js';
        $bootstrap = new Alias('bootstrap', new FileAssetResource($url, $url), [$shim]);

        $config = clone $this->config;
        $config->addAlias($bootstrap);

        $this->assertJavascriptEquals('shim.js', $config);
    }

    public function testEntryPoint()
    {
        $id = 'xxx';
        $resource = new FileOnDemandAssetResource($id, 'console.log(\'hello\');', $this->tempPath.'/hello.js');
        $entryPoint = new EntryPoint($id, $resource);

        $config = clone $this->config;
        $config->addEntryPoint($entryPoint);

        $this->assertJavascriptEquals('entryPoints.js', $config, $entryPoint);
    }

    public function testUrlCacheBust()
    {
        $this->markTestSkipped();
        $url = '/js/jquery.min.js';
        $jquery = new Alias('jquery', new FileAssetResource($url, $url));

        $config = clone $this->config;
        $config->addAlias($jquery);
        //$this->urlProcessor->setCacheBustingEnabled(true);

        $this->assertJavascriptEquals('cacheBusting.js', $config);
    }

    /**
     */
    private function assertJavascriptEquals(
        string $expectedJsFile,
        ConfigurationDefinition $config,
        EntryPoint $entryPoint = null
    ) {
        $id = 'xxx';
        $entryPoint = $entryPoint ?: new EntryPoint(
            $id,
            new FileOnDemandAssetResource($id, '', $this->tempPath.'/xxx.js'));

        $rootPath = \dirname(__DIR__, 2);
        $expectedJs = file_get_contents($rootPath. '/Tests/fixtures/requirejs/' . $expectedJsFile);

        $loader = new RequireJSLoader();
        $compiledJs = $loader->getPageJavascript($entryPoint, $config);

        $this->assertEquals(trim($expectedJs), trim($compiledJs));
    }

    /** @var ConfigurationDefinition */
    private $config;

    ///** @var UrlProcessor */
    //protected $urlProcessor;

    /** @var string */
    protected $workingPath;

    /** @var string */
    protected $tempPath;

    public function setUp()
    {
        parent::setUp();

        $this->workingPath = \dirname(__DIR__, 2);
        $this->tempPath = \dirname(__DIR__, 3).'/var/tmp';

        $this->config = new ConfigurationDefinition('desktop', $this->workingPath);
    }
}

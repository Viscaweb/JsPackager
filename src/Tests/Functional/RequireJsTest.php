<?php

namespace Visca\JsPackager\Tests;

use Doctrine\Common\Cache\VoidCache;
use PHPUnit_Framework_TestCase;
use Visca\JsPackager\Compiler\Url\UrlProcessor;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\Alias;
use Visca\JsPackager\Model\FileResource;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Compiler\RequireJS;
use Visca\JsPackager\Model\Shim;
use Visca\JsPackager\Model\StringResource;


/**
 * Class RequireJsTest
 */
class RequireJsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RequireJS
     */
    protected $compiler;

    public function setUp()
    {
        parent::setUp();

        $urlProcessor = new UrlProcessor(new VoidCache(), '');
        $urlProcessor->setCacheBustingEnabled(false);
        $this->compiler = new RequireJS($urlProcessor);
    }

    /**
     * @test Test an empty config
     */
    public function testEmptyConfig()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');

        $entryPoint = new EntryPoint('xxx', new StringResource(''));

        $output = $this->compiler->compile($entryPoint, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/emptyConfig.js');

        $this->assertEquals($expected, $output);
    }

    public function testBaseUrl()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');
        $config->setOutputPublicPath('/web/');

        $entryPoint = new EntryPoint('xxx', new StringResource(''));
        $output = $this->compiler->compile($entryPoint, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/baseUrl.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Test output of aliases
     */
    public function testAlias()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');

        $resource = new FileResource('js/jquery.min.js');

        $jquery = new Alias('jquery', $resource);
        $config->addAlias($jquery);

        $entryPoint = new EntryPoint('xxx', new StringResource(''));
        $output = $this->compiler->compile($entryPoint, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/alias.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Test output of shims
     */
    public function testShim()
    {
        $config = new ConfigurationDefinition('desktop', 'prod');

        $resource = new FileResource('js/bootstrap.min.js');
        $shim = new Shim();
        $shim->setGlobalVariable('$')
            ->setModuleName('jquery');

        $bootstrap = new Alias('bootstrap', $resource, [$shim]);

        $config->addAlias($bootstrap);

        $entryPoint = new EntryPoint('xxx', new StringResource(''));
        $output = $this->compiler->compile($entryPoint, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/shim.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests an entry point is appended inline in the output.
     */
    public function testEntryPoint()
    {
        $pageName = 'xxx';

        $resource = new StringResource('console.log(\'hello\');');
        $entryPoint = new EntryPoint($pageName, $resource);

        $config = new ConfigurationDefinition('desktop', 'prod');
        $config->addEntryPoint($entryPoint);

        $entryPoint = new EntryPoint('xxx', new StringResource(''));
        $output = $this->compiler->compile($entryPoint, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/entryPoints.js');

        $this->assertEquals($expected, $output);
    }
}

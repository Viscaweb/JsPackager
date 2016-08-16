<?php

namespace Visca\JsPackager\Tests;

use PHPUnit_Framework_TestCase;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\ResourceJs;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Compiler\RequireJS;


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

        $this->compiler = new RequireJS();
    }

    /**
     * @test Test an empty config
     */
    public function testEmptyConfig()
    {
        $config = new ConfigurationDefinition();

        $pageName = 'xxx';
        $output = $this->compiler->compile($pageName, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/emptyConfig.js');

        $this->assertEquals($expected, $output);
    }

    public function testBaseUrl()
    {
        $config = new ConfigurationDefinition();
        $config->setOutputPublicPath('/web/');

        $pageName = 'xxx';
        $output = $this->compiler->compile($pageName, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/baseUrl.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Test output of aliases
     */
    public function testAlias()
    {
        $config = new ConfigurationDefinition();
        
        $jquery = new ResourceJs();
        $jquery->setAlias('jquery');
        $jquery->setPath('js/jquery.min.js');
        
        $config->addAlias($jquery);

        $pageName = 'xxx';
        $output = $this->compiler->compile($pageName, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/alias.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Test output of shims
     */
    public function testShim()
    {
        $config = new ConfigurationDefinition();

        $bootstrap = new ResourceJs();
        $bootstrap
            ->setAlias('bootstrap')
            ->setPath('js/bootstrap.min.js')
            ->setShims(['jquery']);

        $config->addAlias($bootstrap);

        $pageName = 'xxx';
        $output = $this->compiler->compile($pageName, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/shim.js');

        $this->assertEquals($expected, $output);
    }

    /**
     * @test Tests an entry point is appended inline in the output.
     */
    public function testEntryPoint()
    {
        $pageName = 'xxx';

        $entryPoint = new EntryPoint();
        $entryPoint->setName($pageName);
        $entryPoint->setContent('console.log(\'hello\');');

        $config = new ConfigurationDefinition();
        $config->addEntryPoint($entryPoint);


        $output = $this->compiler->compile($pageName, $config);

        $expected = file_get_contents(__DIR__.'/requireJsExpected/entryPoints.js');

        $this->assertEquals($expected, $output);
    }
}

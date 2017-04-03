<?php

namespace Visca\JsPackager\Tests\Functional;

use Doctrine\Common\Cache\ArrayCache;
use Visca\JsPackager\Compiler\Url\UrlProcessor;
use Visca\JsPackager\Compiler\Webpack\WebpackCompiler;
use Visca\JsPackager\Compiler\Webpack\WebpackConfig;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\StringResource;

class WebpackCompilerTest extends \PHPUnit_Framework_TestCase
{

    public function test(){
        $this->markTestIncomplete('to be completed');

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(realpath(__DIR__.'/../../Resources')));
        $webpackTemplate = 'webpack.config.yml.dist';
        $rootDir = realpath(__DIR__.'/../../');
        $webpackConfig = new WebpackConfig($twig, $rootDir, $webpackTemplate);
        $nodePath = 'node';
        $debug = false;
        $urlProcessor = new UrlProcessor(new ArrayCache(), $rootDir);

        $config = new ConfigurationDefinition('desktop', 'prod');
        $config->setBuildOutputPath('/Volumes/develop/tap-livescore/libe/Volumes/develop/tap-livescore/life-js-packager/src/Resources/tmp/');

        $config->addEntryPoint(new EntryPoint('xxx', new StringResource('var name="raul";')));

        $webpackCompiler = new WebpackCompiler($webpackConfig, $rootDir, $nodePath, $debug, $urlProcessor);
        $content = $webpackCompiler->compileCollection($config);

        var_dump($content);
    }

}
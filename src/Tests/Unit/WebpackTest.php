<?php

namespace Visca\JsPackager\Tests\Functional;

use Visca\JsPackager\Compiler\Storage\CompiledFileStorage;
use Visca\JsPackager\Compiler\Storage\Exceptions\UnableToProvideScriptException;
use Visca\JsPackager\Compiler\Webpack;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;

class WebpackTest extends \PHPUnit_Framework_TestCase
{

    public function testNotFoundJsThrowsException(){
        $this->expectException(UnableToProvideScriptException::class);
        $this->compile($this->createStorage(null));
    }

    public function testValidJsReturnsTheContent()
    {
        $javascript = 'myJsFunction(1);';

        $this->assertEquals(
            $this->compile($this->createStorage($javascript)),
            $javascript
        );
    }

    /**
     * @param null $jsToReturn
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createStorage($jsToReturn = null){
        $storage = $this->getMockBuilder(CompiledFileStorage::class)->getMock();

        if ($jsToReturn === null){
            $storage->method('contains')->willReturn(false);
            $storage->method('fetch')->willReturn(false);
        } else {
            $storage->method('contains')->willReturn(true);
            $storage->method('fetch')->willReturn($jsToReturn);
        }

        return $storage;
    }

    /**
     * @param $storage
     *
     * @return string
     */
    private function compile($storage)
    {
        /** @var EntryPoint|\PHPUnit_Framework_MockObject_MockObject $entryPoint */
        $entryPoint = $this->getMockBuilder(EntryPoint::class)->disableOriginalConstructor()->getMock();

        /** @var ConfigurationDefinition|\PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMockBuilder(ConfigurationDefinition::class)->disableOriginalConstructor()->getMock();

        return (new Webpack($storage))->compile($entryPoint, $config);
    }
}

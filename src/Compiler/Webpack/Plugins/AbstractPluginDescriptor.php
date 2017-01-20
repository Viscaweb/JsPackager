<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class AbstractPluginDescriptor
 */
abstract class AbstractPluginDescriptor implements PluginDescriptorInterface
{
    public function getRequireCall()
    {
        $moduleName = $this->getModuleName();
        $varName = str_replace('-', ' ', $moduleName);

        $output = 'var '.
            $this->dashesToCamelCase($moduleName).
            ' = require(\''.$moduleName.'\');';

        return $output;
    }

    private function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}

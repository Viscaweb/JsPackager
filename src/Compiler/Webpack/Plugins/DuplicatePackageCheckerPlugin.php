<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

/**
 * Class DuplicatePackageCheckerPlugin
 */
class DuplicatePackageCheckerPlugin extends AbstractPluginDescriptor
{
    public function getName()
    {
        return 'duplicatePackageCheckerWebpackPlugin';
    }

    public function getModuleName()
    {
        return 'duplicate-package-checker-webpack-plugin';
    }

    public function getOptions()
    {
        return [];
    }
}

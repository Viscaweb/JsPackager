<?php

namespace Visca\JsPackager\Webpack\Configuration\Plugins;

class DuplicatePackageCheckerPlugin extends AbstractPluginDescriptor
{
    public function name()
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

<?php

namespace Visca\JsPackager\Webpack\Configuration\Plugins;

use Visca\JsPackager\Configuration\Shim;

/**
 * Class ProvidePlugin
 */
class ProvidePlugin extends AbstractPluginDescriptor
{
    /** @var Shim[] */
    protected $shims;

    /**
     * @param Shim[] $shims
     */
    public function __construct(array $shims)
    {
        $this->shims = $shims;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return 'webpack-bundle-analyzer';
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return 'webpack.ProvidePlugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $options = [];

        foreach ($this->shims as $shim) {
            $options[$shim->getGlobalVariable()] = $shim->getModuleName();
        }

        return $options;
    }
}

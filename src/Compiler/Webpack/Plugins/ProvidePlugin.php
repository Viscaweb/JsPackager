<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

use Visca\JsPackager\Model\Shim;

/**
 * Class ProvidePlugin
 */
class ProvidePlugin extends AbstractPluginDescriptor
{
    /** @var Shim[] */
    protected $shims;

    /**
     * ProvidePlugin constructor.
     *
     * @param Shim[] $shims
     */
    public function __construct($shims)
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
    public function getName()
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

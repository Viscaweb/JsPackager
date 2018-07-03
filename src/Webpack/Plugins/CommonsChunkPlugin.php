<?php

namespace Visca\JsPackager\Webpack\Plugins;

use Visca\JsPackager\Configuration\ConfigurationDefinition;

class CommonsChunkPlugin extends AbstractPluginDescriptor
{
    /** @var ConfigurationDefinition */
    private $config;

    public function __construct(ConfigurationDefinition $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return 'webpack';
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return 'webpack.optimize.CommonsChunkPlugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        // Look for entryPoints named 'vendorX-****'
        $entryPoints = $this->config->getEntryPoints();
        if (count($entryPoints) > 0) {
            foreach ($entryPoints as $entryPoint) {
                $name = $entryPoint->getName();
                $pattern = '/^vendor[0-9]+.*$/';
                if (preg_match($pattern, $name)) {
                    $commonsName = $name;
                    break;
                }
            }

            return [
                'name' => $name,
                'filename' => 'commons.[hash].js'
            ];
        }

        return null;
    }
}

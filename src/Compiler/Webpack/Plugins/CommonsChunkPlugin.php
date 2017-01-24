<?php

namespace Visca\JsPackager\Compiler\Webpack\Plugins;

use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class CommonsChunkPlugin
 */
class CommonsChunkPlugin extends AbstractPluginDescriptor
{
    /** @var ConfigurationDefinition */
    private $config;

    /**
     * CommonsChunkPlugin constructor.
     *
     * @param ConfigurationDefinition $config
     */
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
    public function getName()
    {
        return 'webpack.optimize.CommonsChunkPlugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        // Look for entryPoints named 'vendorX-****'
        foreach ($this->config->getEntryPoints() as $entryPoint) {
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
}

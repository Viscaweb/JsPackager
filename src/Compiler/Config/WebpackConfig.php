<?php

namespace Visca\JsPackager\Compiler\Config;

use Twig_Environment;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class WebpackConfig
 */
class WebpackConfig
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var string */
    protected $template;

    /** @var string */
    protected $temporalPath;

    /**
     * WebpackConfig constructor.
     *
     * @param Twig_Environment $twig
     * @param string           $template      Path to config.js template
     * @param string           $temporalPath  Path to where object will store temporal
     *                                        content.
     */
    public function __construct(Twig_Environment $twig, $template, $temporalPath = './')
    {
        $this->twig = $twig;
        $this->template = $template;
        $this->temporalPath = rtrim($temporalPath, '/').'/';
    }

    /**
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function compile(ConfigurationDefinition $config)
    {
        $outputPath = '';

        // Module Alias
        // ------------
        $aliases = $config->getAlias();
        $alias = [];
        $publicPath = rtrim($outputPath, '/');
        if (count($aliases) > 0) {
            foreach ($aliases as $resource) {
                $path = $resource->getPath();
                $shims = $resource->getShims();
                if (count($shims) > 0) {
                    $shimCollection = [];
                    foreach ($shims as $shim) {
                        if ($shim instanceof Shim) {
                            $shimCollection[] = $shim->getGlobalVariable().'='.$shim->getModuleName();
                        }
                    }
                    $path = 'imports?'.implode('&', $shimCollection).'!'.$publicPath.$path;
                } else {
                    $path = $publicPath.$path;
                }

                $alias[$resource->getAlias()] = $path;
            }
        }

        $entryPoints = [];

        /** @var EntryPoint $entryPoint */
        foreach ($config->getEntryPoints() as $entryPoint) {

            if (empty($entryPoint->getPath()) === false) {
                $path = $entryPoint->getPath();
            } else {
                $path = $this->saveTemporalEntryPoint($entryPoint);
            }

            $entryPoints[] = [
                'name' => $entryPoint->getName(),
                'path' => $path
            ];
        }


        $output = $this->twig->render(
            $this->template,
//            'ViscaJsEntryPointBundle::webpack.config.sample.js.twig',
            [
                'entryPoints' => $entryPoints,
                'outputPath' => $config->getBuildOutputPath(),
                'alias' => $alias,
            ]
        );

        return $output;
    }

    /**
     * @param EntryPoint $entryPoint
     *
     * @return string
     */
    private function saveTemporalEntryPoint(EntryPoint $entryPoint)
    {
        $filename = $entryPoint->getName().'.entry_point.js';

        $path = $this->temporalPath.$filename;

        file_put_contents($path, $entryPoint->getContent());

        return $path;
    }
}

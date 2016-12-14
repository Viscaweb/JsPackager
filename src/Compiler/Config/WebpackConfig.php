<?php

namespace Visca\JsPackager\Compiler\Config;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Twig_Environment;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\EntryPointContent;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class WebpackConfig
 */
class WebpackConfig
{
    const IMPORTS_LOADER = 'imports-loader';
    /** @var string */
    protected $rootDir;

    /** @var Twig_Environment */
    protected $twig;

    /** @var string */
    protected $templatePath;

    /** @var FileLocator */
    protected $fileLocator;

    /**
     * WebpackConfig constructor.
     *
     * @param Twig_Environment $twig
     * @param string           $templatePath  Path to config.js template
     */
    public function __construct($rootDir, Twig_Environment $twig, $templatePath/*, FileLocator $fileLocator*/)
    {
        $this->rootDir = dirname($rootDir);
        $this->twig = $twig;
        // pfff, i don't like this, but i can't find any other
        // way to pass '@' from yml
        $this->templatePath = '@'.$templatePath;
//        $this->fileLocator = $fileLocator;
    }

    /**
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function compile(ConfigurationDefinition $config)
    {
        // Module Alias
        // ------------
        $aliases = $config->getAlias();
        $alias = [];
        $publicPath = rtrim($this->rootDir.'/web', '/');
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
                    $path = self::IMPORTS_LOADER.'?'.implode('&', $shimCollection).'!'.$publicPath.$path;
                } else {
                    $path = $publicPath.$path;
                }

                $alias[$resource->getAlias()] = $path;
            }
        }

        $entryPoints = [];

        // Prepare GlobalInline content
        $globalInlineEntryPoint = $config->getEntryPointsGlobalInline();
        if (count($globalInlineEntryPoint) > 0) {
            $entryPointGlobalToInline = '';
            foreach ($globalInlineEntryPoint as $entryPoint) {
                if ($entryPoint instanceof EntryPointContent) {
                    $entryPointGlobalToInline.= $entryPoint->getContent();
                }
            }
        }

        /** @var EntryPoint $entryPoint */
        foreach ($config->getEntryPoints() as $ep) {

            if ($ep instanceof EntryPointFile) {
                $path = $ep->getPath();
                if (!empty($entryPointGlobalToInline)) {
                    if (file_exists($path)) {
                        $content = file_get_contents($path);
                        $epC = new EntryPointContent(
                            $ep->getName(),
                            $entryPointGlobalToInline."\n".
                                $content
                            );
                        $path = $this->saveTemporalEntryPoint($epC);
                    }
                }
            } elseif ($ep instanceof EntryPointContent) {
                if (!empty($entryPointGlobalToInline)) {
                    $ep->setContent(
                        $entryPointGlobalToInline."\n".$ep->getContent()
                    );
                }

                $path = $this->saveTemporalEntryPoint($ep);
            }

            $entryPoints[] = [
                'name' => $ep->getName(),
                'path' => $path
            ];
        }


        $output = $this->twig->render(
            $this->templatePath,
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
     * @param EntryPointContent $entryPoint
     *
     * @return string
     */
    private function saveTemporalEntryPoint(EntryPointContent $entryPoint)
    {
        $filename = $entryPoint->getName().'.entry_point.js';

        $path = $this->getTemporalPath().'/'.$filename;

        file_put_contents($path, $entryPoint->getContent());

        return $path;
    }

    private function getTemporalPath()
    {
        return '/Volumes/Develop/GitRepos/viscaweb/life/tmp';
        return sys_get_temp_dir();
    }

    /**
     * @param string $base
     * @param string $path
     *
     * @return string
     */
    private function getRelativePath($base, $path) {
        // Detect directory separator
        $separator = substr($base, 0, 1);
        $base = array_slice(explode($separator, rtrim($base,$separator)),1);
        $path = array_slice(explode($separator, rtrim($path,$separator)),1);

        return $separator.implode($separator, array_slice($path, count($base)));
    }
}

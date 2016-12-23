<?php

namespace Visca\JsPackager\Compiler\Config;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Twig_Environment;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\Shim;
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
     * @param string           $rootDir
     * @param Twig_Environment $twig
     * @param string           $templatePath  Path to config.js template
     */
    public function __construct(Twig_Environment $twig, $rootDir, $templatePath)
    {
        $this->rootDir = dirname($rootDir);
        $this->twig = $twig;
        // pfff, i don't like this, but i can't find any other
        // way to pass '@' from yml
        $this->templatePath = $templatePath;
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
        $wpAlias = [];
        $publicPath = rtrim($this->rootDir.'/web', '/');
        if (count($aliases) > 0) {
            foreach ($aliases as $_alias) {
                $resource = $_alias->getResource();
                $path = ltrim($resource->getPath(), '/');
                $shims = $_alias->getShims();
                /* @TODO does not work totally... better put this in webpack.config.js instead.
                if (count($shims) > 0) {
                    $shimCollection = [];
                    foreach ($shims as $shim) {
                        if ($shim instanceof Shim) {
                            $shimCollection[] = $shim->getGlobalVariable().'='.$shim->getModuleName();
                        }
                    }
                    $path = self::IMPORTS_LOADER.'?'.implode('&', $shimCollection).'!'.$publicPath.$path;
                } else {
                */
                    $path = $publicPath.'/'.$path;
//                }

                $wpAlias[$_alias->getName()] = $path;
            }
        }

        $entryPoints = [];

        // Prepare GlobalInline content
        $globalInlineEntryPoint = $config->getEntryPointsGlobalInline();
        if (count($globalInlineEntryPoint) > 0) {
            $entryPointGlobalToInline = '';
            foreach ($globalInlineEntryPoint as $entryPoint) {
                $entryPointGlobalToInline.= $entryPoint->getResource()->getContent();
            }
        }

        /** @var EntryPoint $entryPoint */
        foreach ($config->getEntryPoints() as $ep) {

            $resource = $ep->getResource();

            $content = '';
            if (!empty($entryPointGlobalToInline)) {
                $content.= $entryPointGlobalToInline;
            }
            $content.= $resource->getContent();

            $path = $this->saveTemporalEntryPoint($ep->getName(), $content);

            $entryPoints[] = [
                'name' => $ep->getName(),
                'path' => $path
            ];
        }


        $output = $this->twig->render(
            $this->templatePath,
            [
                'entryPoints' => $entryPoints,
                'outputPath' => $config->getBuildOutputPath(),
                'publicPath' => $config->getOutputPublicPath(),
                'alias' => $wpAlias,
            ]
        );

        $path = $this->getTemporalPath().'/webpack.config.js';

        file_put_contents($path, $output);

        return $path;
    }

    /**
     * @param EntryPointContent $entryPoint
     *
     * @return string
     */
    private function saveTemporalEntryPoint($entryPointName, $content)
    {
        $filename = $entryPointName.'.entry_point.js';

        $path = $this->getTemporalPath().'/'.$filename;

        file_put_contents($path, $content);

        return $path;
    }

    public function getTemporalPath()
    {
        return $this->rootDir.'/tmp';
//        return sys_get_temp_dir();
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

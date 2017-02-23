<?php

namespace Visca\JsPackager\Compiler\Webpack;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Twig_Environment;
use Visca\JsPackager\Compiler\Webpack\Plugins\BundleAnalyzerPlugin;
use Visca\JsPackager\Compiler\Webpack\Plugins\CommonsChunkPlugin;
use Visca\JsPackager\Compiler\Webpack\Plugins\DuplicatePackageCheckerPlugin;
use Visca\JsPackager\Compiler\Webpack\Plugins\MinChunkSizePlugin;
use Visca\JsPackager\Compiler\Webpack\Plugins\UglifyJsPlugin;
use Visca\JsPackager\Compiler\Webpack\Loaders\JsonLoader;
use Visca\JsPackager\Model\AliasResource;
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

    /** @var string */
    protected $temporalPath;

    /**
     * WebpackConfig constructor.
     *
     * @param string           $rootDir
     * @param Twig_Environment $twig
     * @param string           $templatePath Path to config.js template
     * @param string|null      $temporalPath Path used to generate temporal assets.
     */
    public function __construct(Twig_Environment $twig, $rootDir, $templatePath, $temporalPath = null)
    {
        $this->rootDir = dirname($rootDir);
        $this->twig = $twig;
        // pfff, i don't like this, but i can't find any other
        // way to pass '@' from yml
        $this->templatePath = $templatePath;

        if ($temporalPath !== null) {
            if (!is_dir($temporalPath)) {
                mkdir($temporalPath, 0777, true);
            }

            $temporalPath = realpath($temporalPath);
        }
        $this->temporalPath = $temporalPath;
    }

    /**
     * @param ConfigurationDefinition $config Configuration file.
     * @param                         bool    Enables some debugging info in the output.
     *
     * @return string
     */
    public function compile(ConfigurationDefinition $config, $debug = false)
    {
        // Module Alias
        // ------------
        $aliases = $config->getAlias();
        $wpAlias = [];
        $publicPath = rtrim($this->rootDir.'/web', '/');
        if (count($aliases) > 0) {
            foreach ($aliases as $alias) {
                $resource = $alias->getResource();
                $path = ltrim($resource->getPath(), '/');
                $shims = $alias->getShims();
                /* @TODO does not work totally... better put this in webpack.config.js instead.
                 * if (count($shims) > 0) {
                 * $shimCollection = [];
                 * foreach ($shims as $shim) {
                 * if ($shim instanceof Shim) {
                 * $shimCollection[] = $shim->getGlobalVariable().'='.$shim->getModuleName();
                 * }
                 * }
                 * $path = self::IMPORTS_LOADER.'?'.implode('&', $shimCollection).'!'.$publicPath.$path;
                 * } else {
                 */
                $path = $publicPath.'/'.$path;
//                }

                $wpAlias[$alias->getName()] = $path;
            }
        }

        $entryPoints = [];

        // Prepare GlobalInline content
        $globalInlineEntryPoint = $config->getEntryPointsGlobalInline();
        if (count($globalInlineEntryPoint) > 0) {
            $entryPointGlobalToInline = '';
            foreach ($globalInlineEntryPoint as $entryPoint) {
                $entryPointGlobalToInline .= $entryPoint->getResource()->getContent();
            }
        }

        /** @var EntryPoint $entryPoint */
        foreach ($config->getEntryPoints() as $ep) {

            $resource = $ep->getResource();

            if ($resource instanceof AliasResource) {
                $entryPoints[] = [
                    'name' => $ep->getName(),
                    'aliases' => $resource->getPath()
                ];

                continue;
            }

            $content = '';
            if (!empty($entryPointGlobalToInline)) {
                $content .= $entryPointGlobalToInline;
            }
            $content .= $resource->getContent();

            $path = $this->saveTemporalEntryPoint($ep->getName(), $content);

            $entryPoints[] = [
                'name' => $ep->getName(),
                'path' => $path
            ];
        }

        // -----------------
        // Plugins
        // -----------------
        $plugins = [];
        $plugins[] = new CommonsChunkPlugin($config);
        if ($config->isMinifyEnabled()) {
            $plugins[] = new UglifyJsPlugin();
        }
        $plugins[] = new MinChunkSizePlugin();
        $plugins[] = new DuplicatePackageCheckerPlugin();

        if ($debug) {
            $plugins[] = new BundleAnalyzerPlugin();
        }

        // -----------------------
        // Loaders
        // -----------------------
        $loaders = [];
        $loaders[] = new JsonLoader();

        // -----------------------
        // require() calls to make
        // -----------------------
        $jsModules = [];
        foreach ($plugins as $plugin) {
            $moduleName = $plugin->getModuleName();
            if ($moduleName !== null && !isset($jsModules[$moduleName])) {
                $jsModules[$moduleName] = $plugin->getRequireCall();
            }
        }


        $output = $this->twig->render(
            $this->templatePath,
            [
                'jsModules' => $jsModules,
                'entryPoints' => $entryPoints,
                'outputPath' => $config->getBuildOutputPath(),
                'publicPath' => $config->getOutputPublicPath(),
                'alias' => $wpAlias,
                'loaders' => $loaders,
                'plugins' => $plugins
            ]
        );

        $path = $this->getTemporalPath().'/webpack.config.'.$config->getName().'.js';

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
        return $this->temporalPath === null
            ? sys_get_temp_dir()
            : $this->temporalPath;
    }

    /**
     * @param string $base
     * @param string $path
     *
     * @return string
     */
    private function getRelativePath($base, $path)
    {
        // Detect directory separator
        $separator = substr($base, 0, 1);
        $base = array_slice(explode($separator, rtrim($base, $separator)), 1);
        $path = array_slice(explode($separator, rtrim($path, $separator)), 1);

        return $separator.implode($separator, array_slice($path, count($base)));
    }
}

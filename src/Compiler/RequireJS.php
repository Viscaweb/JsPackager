<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Model\Alias;
use Visca\JsPackager\Model\PackageStats;
use Visca\JsPackager\Model\Shim;
use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Compiler\Url\UrlProcessor;

class RequireJS implements CompilerInterface
{
    /** @var UrlProcessor */
    protected $urlProcessor;

    /**
     * RequireJS constructor.
     *
     * @param UrlProcessor $urlProcessor
     */
    public function __construct(UrlProcessor $urlProcessor)
    {
        $this->urlProcessor = $urlProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'requirejs';
    }

    /**
     * {@inheritdoc}
     */
    public function compile(EntryPoint $entryPoint, ConfigurationDefinition $config)
    {
        $pageName = $entryPoint->getName();

        $script = sprintf(
            "<!-- JS for %s -->\n",
            $pageName
        );
        $script .= $this->addScriptTag('/bundles/app/js/common/requirejs.js', $config);
        $script .= "\n";

        // Entry point configuration
        // Do we need to add more external script tags?
        $externals = $config->getEntryPointsGlobalIncludes();
        if (is_array($externals)) {
            foreach ($externals as $ep) {
                $script .= $this->addScript($ep->getResource()->getPath(), $config);
                $script .= "\n";
            }
        }

        // See if entry point requires external dependencies
        foreach ($config->getEntryPoints() as $ep) {
            foreach ($ep->getExternalResources() as $urlResource) {
                $script .= '<script type="text/javascript" src="'.$urlResource->getUrl().'"></script>';
            }
        }

        $script .= '<script>';

        // Include RequireJS inline configuration
        $script .= $this->compileRequireJsConfig($config)."\n";

        // Include inline Javascript page entry point
        foreach ($config->getEntryPointsGlobalInline() as $ep) {
            $script .= $ep->getResource()->getContent()."\n";
        }


        foreach ($config->getEntryPoints() as $ep) {
            if ($ep->getName() == $pageName) {

                $script .= $ep->getResource()->getContent();
                $script .= "\n";
            }
        }
        $script .= '</script>';
        $script .= "\n";
        $script .= '<!-- END of JS -->';

        return $script;
    }

    /**
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    private function compileRequireJsConfig(ConfigurationDefinition $config)
    {
        $data = [];

        $outputPublicPath = $config->getOutputPublicPath();
        if ($outputPublicPath !== null) {
            $outputPublicPath = trim($outputPublicPath, '/');

            $data['baseUrl'] = empty($outputPublicPath) ? '/' : '/'.$outputPublicPath.'/';
        }

        $aliases = $config->getAlias();
        if (is_array($aliases)) {
            /** @var Alias $alias */
            foreach ($aliases as $alias) {
                $path = $alias->getResource()->getPath();
                if ($path !== null) {
                    $aliasName = $alias->getName();
                    $aliasName = str_replace('$', '', $aliasName);
                    $data['paths'][$aliasName] = $this->urlProcessor->processUrl($path, $config);
                }

                $shims = $alias->getShims();
                if (count($shims) > 0) {
                    $modules = [];
                    /** @var Shim $shim */
                    foreach ($shims as $shim) {
                        $modules[] = $shim->getModuleName();
                    }

                    $data['shim'][$alias->getName()] = ['deps' => $modules];
                }
            }
        }

        $data['waitSeconds'] = 15;

        $script =
            'requirejs.config('.json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ).');';

        return $script;
    }

    /**
     * @param string                       $url
     * @param ConfigurationDefinition|null $config
     *
     * @return string
     */
    private function addScriptTag($url, ConfigurationDefinition $config = null)
    {
        if(!is_null($config) && !is_null($this->urlProcessor)){
            $url = $this->urlProcessor->processUrl($url, $config);
        }

        $tag = '<script src="'.$url.'"></script>';

        return $tag;
    }
}

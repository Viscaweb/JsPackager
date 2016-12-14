<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Configuration\EntryPointContent;
use Visca\JsPackager\Configuration\EntryPointFile;
use Visca\JsPackager\Configuration\ResourceJs;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Compiler\Url\UrlProcessor;

/**
 * Class RequireJSManager
 */
class RequireJS extends AbstractCompiler
{
    /** @var UrlProcessor */
    protected $urlProcessor;

    /**
     * AbstractCompiler constructor.
     *
     * @param UrlProcessor $urlProcessor
     */
    public function __construct(UrlProcessor $urlProcessor)
    {
        $this->urlProcessor = $urlProcessor;
    }

    /**
     * @param string                  $pageName
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function compile($pageName, ConfigurationDefinition $config)
    {
        $this->debug = true;

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
                if ($ep instanceof EntryPointFile) {
                    $script .= $this->addScriptTagExtended($ep->getPath(), $config);
                    $script .= "\n";
                }
            }
        }

        $script .= '<script>';

        // Include RequireJS inline configuration
        $script .= $this->compileRequireJsConfig($config)."\n";

        // Include inline Javascript page entry point
        foreach ($config->getEntryPointsGlobalInline() as $ep) {
            if ($ep instanceof EntryPointContent) {
                $script .= $ep->getContent()."\n";
            }
        }


        foreach ($config->getEntryPoints() as $ep) {
            if ($ep->getName() == $pageName) {

                if ($ep instanceof EntryPointContent) {
                    $script .= $ep->getContent();
                    $script .= "\n";
                }
            }
        }
        $script .= '</script>';

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
            $data['baseUrl'] = '/'.trim($outputPublicPath, '/').'/';
        }

        $aliases = $config->getAlias();
        if (is_array($aliases)) {
            /** @var ResourceJs $alias */
            foreach ($aliases as $alias) {
                $path = $alias->getPath();
                if ($path !== null) {
                    $data['paths'][$alias->getAlias()] = $this->urlProcessor->processUrl($path, $config);
                }

                $shims = $alias->getShims();
                if ($shims !== null && is_array($shims)) {
                    $modules = [];
                    /** @var Shim $shim */
                    foreach ($shims as $shim) {
                        $modules[] = $shim->getModuleName();
                    }

                    $data['shim'][$alias->getAlias()] = ['deps' => $modules];
                }
            }
        }

        $script =
            'requirejs.config('.json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ).');';

        return $script;
    }

    /**
     * {@inheritdoc}
     */
    protected function addScriptTagExtended($url, ConfigurationDefinition $config)
    {
        $url = $this->urlProcessor->processUrl($url, $config);

        return parent::addScriptTag($url);
    }
}

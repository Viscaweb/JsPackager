<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Configuration\EntryPointContent;
use Visca\JsPackager\Configuration\EntryPointFile;
use Visca\JsPackager\Model\Alias;
use Visca\JsPackager\Model\Shim;
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
     * @note This packager does not support processing multiple entry points at once.
     * {@inheritdoc}
     */
    public function compile($entryPoints, ConfigurationDefinition $config)
    {
        if (is_array($entryPoints)) {
            throw new \RuntimeException('RequireJS packager does not support processing multiple entrypoints at once.');
        }

        $pageName = $entryPoints->getName();
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
                $script .= $this->addScriptTagExtended($ep->getResource()->getPath(), $config);
                $script .= "\n";
            }
        }

        $script .= '<script>';

//         Include RequireJS inline configuration
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
            $data['baseUrl'] = '/'.trim($outputPublicPath, '/').'/';
        }

        $aliases = $config->getAlias();
        if (is_array($aliases)) {
            /** @var Alias $alias */
            foreach ($aliases as $alias) {
                $path = $alias->getResource()->getPath();
                if ($path !== null) {
                    $data['paths'][$alias->getName()] = $this->urlProcessor->processUrl($path, $config);
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

<?php

namespace Visca\JsPackager\RequireJS;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\Shim;
use Visca\JsPackager\JavascriptLoader;

class RequireJSLoader implements JavascriptLoader
{
    const REQUIREJSLIB = '/bundles/app/js/common/requirejs.js';

    /** @var ConfigurationDefinition */
    private $configuration;

    public function __construct(ConfigurationDefinition $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $script = $this->buildScript($entryPoint);

        return $script;
    }

    private function buildScript(EntryPoint $entryPoint): string
    {
        $script = '<script src="'.self::REQUIREJSLIB.'"></script>';
        $script .= "\n";
        $script.= '<script>';
        $script.= $this->buildRequireJsConfig()."\n";
        $script.= $this->buildEntryPoint($entryPoint);
        $script.= '</script>';

        return $script;
    }

    private function buildRequireJsConfig(): string
    {
        $data = [];

        $outputPublicPath = $this->configuration->getOutputPublicPath();
        if ($outputPublicPath !== null) {
            $outputPublicPath = trim($outputPublicPath, '/');

            $data['baseUrl'] = empty($outputPublicPath) ? '/' : '/'.$outputPublicPath.'/';
        }

        $aliases = $this->configuration->getAlias();
        if (is_array($aliases)) {
            /** @var Alias $alias */
            foreach ($aliases as $alias) {
                $path = $alias->getResource()->getPath();

                if ($path !== null) {
                    $aliasName = $alias->getName();
                    $aliasName = str_replace('$', '', $aliasName);

                    // We need to convert this path (absolute) to a URL.
                    $url = $this->convertPathToURL($path, $this->configuration);
                    //$data['paths'][$aliasName] = $this->urlProcessor->processUrl($url, $this->configuration);
                    $data['paths'][$aliasName] = $url;
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

    private function buildEntryPoint(EntryPoint $entryPoint): string
    {
        $script = '';

        // Include js required for all entry points
        foreach ($this->configuration->getEntryPointsGlobalInline() as $gbEntryPoint) {
            $script.= $gbEntryPoint->getResource()->getContent()."\n";
        }

        // Include js of required entry point
        $pageName = $entryPoint->getName();
        foreach ($this->configuration->getEntryPoints() as $ep) {
            if ($ep->getName() === $pageName) {
                $script .= $ep->getResource()->getContent();
            }
        }

        return !empty($script) ? "\n".$script : '';
    }

    private function convertPathToURL($path, ConfigurationDefinition $config)
    {
        return '/'.str_replace($config->getWorkingPath().$config->getOutputPublicPath(), '', $path);
    }
}

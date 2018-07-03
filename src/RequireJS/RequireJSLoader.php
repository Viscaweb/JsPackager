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

    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $script = $this->buildScript($entryPoint, $configuration);

        return $script;
    }

    private function buildScript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $script = '<script src="'.self::REQUIREJSLIB.'"></script>';
        $script .= "\n";
        $script.= '<script>';
        $script.= $this->buildRequireJsConfig($configuration)."\n";
        $script.= $this->buildEntryPoint($entryPoint, $configuration);
        $script.= '</script>';

        return $script;
    }

    private function buildRequireJsConfig(ConfigurationDefinition $configuration): string
    {
        $data = [];

        $outputPublicPath = $configuration->getOutputPublicPath();
        if ($outputPublicPath !== null) {
            $outputPublicPath = trim($outputPublicPath, '/');

            $data['baseUrl'] = empty($outputPublicPath) ? '/' : '/'.$outputPublicPath.'/';
        }

        $aliases = $configuration->getAlias();
        if (is_array($aliases)) {
            /** @var Alias $alias */
            foreach ($aliases as $alias) {
                $path = $alias->getResource()->getPath();

                if ($path !== null) {
                    $aliasName = $alias->getName();
                    $aliasName = str_replace('$', '', $aliasName);

                    // We need to convert this path (absolute) to a URL.
                    $url = $this->convertPathToURL($path, $configuration);
                    //$data['paths'][$aliasName] = $this->urlProcessor->processUrl($url, $configuration);
                    $data['paths'][$aliasName] = $url;
                }

                $shims = $alias->getShims();
                if (\count($shims) > 0) {
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

    private function buildEntryPoint(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $script = '';

        // Include js required for all entry points
        foreach ($configuration->getEntryPointsGlobalInline() as $gbEntryPoint) {
            $script.= $gbEntryPoint->getResource()->getContent()."\n";
        }

        // Include js of required entry point
        $pageName = $entryPoint->getName();
        foreach ($configuration->getEntryPoints() as $ep) {
            if ($ep->getName() === $pageName) {
                $script .= $ep->getResource()->getContent();
            }
        }

        return !empty($script) ? $script."\n" : '';
    }

    private function convertPathToURL($path, ConfigurationDefinition $config)
    {
//        return '/'.str_replace($config->getWorkingPath().$config->getOutputPublicPath(), '', $path);
        return $path;
    }
}

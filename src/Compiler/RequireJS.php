<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class RequireJSManager
 */
class RequireJS extends AbstractCompiler
{
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
        $script .= $this->addScriptTag('bundles/app/js/common/requirejs.js');
        $script .= "\n";

        // Entry point configuration
        // Do we need to add more external script tags?
        $externals = $config->getEntryPointsGlobalIncludes();
        if (is_array($externals)) {
            foreach ($externals as $url) {
                $script .= $this->addScriptTag($url->getPath());
                $script .= "\n";
            }
        }

        $script .= '<script>';

        // Include RequireJS inline configuration
        $script .= $this->compileRequireJsConfig($config)."\n";

        // Include inline Javascript page entry point
        foreach ($config->getEntryPointsGlobalInline() as $ep) {
            $script .= $ep->getContent()."\n";
        }


        foreach ($config->getEntryPoints() as $entryPoint) {
            if ($entryPoint->getName() == $pageName) {
                $script .= $entryPoint->getContent();
                $script .= "\n";
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
            foreach ($aliases as $alias) {
                //$aliasInfo = $this->getResolveAliasInfo($value);

                $path = $alias->getPath();
                if ($path !== null) {
                    $data['paths'][$alias->getAlias()] = $path;
                }

                $shims = $alias->getShims();
                if ($shims !== null && is_array($shims)) {
                    $data['shim'][$alias->getAlias()] = ['deps' => $shims];
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
}

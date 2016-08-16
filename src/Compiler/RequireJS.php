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

        // Entry point configuration
        // Do we need to add more external script tags?
        $externals = $config->getEntryPointsGlobalIncludes();
        if (is_array($externals)) {
            foreach ($externals as $url) {
                $script .= $this->addScriptTag($url->getPath());
            }
        }

        $script .= '<script>';

        // Include RequireJS inline configuration
        $script .= $this->compileRequireJsConfig($config);

        // Include inline Javascript page entry point
        $script .= implode("\n", $config->getEntryPointsGlobalInclude());
        foreach ($config->getEntryPoints() as $entryPoint) {
            if ($entryPoint->getName() == $pageName) {
                $script .= $entryPoint->getContent();
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
            $data['baseUrl'] = $outputPublicPath;
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

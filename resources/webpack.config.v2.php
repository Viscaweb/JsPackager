<?php
/**
 * @var $modules
 * @var string $outputPath
 * @var string $outputPublicPath
 * @var \Visca\JsPackager\Configuration\EntryPoint[] $entryPoints
 * @var \Visca\JsPackager\Configuration\Alias[] $aliases
 */

use Visca\JsPackager\TemplateEngine\PHPEngine;

echo "'use strict';\n\n";

foreach ($modules as $module) {
    echo $module."\n";
}

echo 'const outputConfig = '.PHPEngine::jsonEncode([
        'filename' => '[name].dist.js',
        'chunkFilename' => '[id].dist.js',
        'path' => $outputPath,
        'publicPath' => $outputPublicPath,
    ]).";\n";

echo "\n";
echo "const entriesConfig = {};\n";
foreach ($entryPoints as $entry) {
    $resource = $entry->getResource();
    switch (true) {
        case ($resource instanceof \Visca\JsPackager\Resource\FileAssetResource):
        case ($resource instanceof \Visca\JsPackager\Resource\FileOnDemandAssetResource):
            /** @var \Visca\JsPackager\Resource\FileAssetResource $resource */
            $value = '"'.$resource->getPath().'"';
            break;

        case ($resource instanceof \Visca\JsPackager\Resource\AliasAssetResource):
            /** @var \Visca\JsPackager\Resource\AliasAssetResource $resource */
            $value = PHPEngine::jsonEncode($resource->getAliases());
            break;
    }
    echo "entriesConfig['".$entry->getName()."'] = ".$value.";\n";
}

echo "\n";
echo "const aliasConfig = {};\n";
foreach ($aliases as $alias) {
    $resource = $alias->getResource();
    switch (true) {
        case ($resource instanceof \Visca\JsPackager\Resource\FileAssetResource):
        case ($resource instanceof \Visca\JsPackager\Resource\FileOnDemandAssetResource):
            /** @var \Visca\JsPackager\Resource\FileAssetResource $resource */
            $value = '"'.$resource->getPath().'"';
            break;

        case ($resource instanceof \Visca\JsPackager\Resource\AliasAssetResource):
            /** @var \Visca\JsPackager\Resource\AliasAssetResource $resource */
            $value = PHPEngine::jsonEncode($resource->getAliases());
            break;
    }
    echo "aliasConfig['".$alias->getName()."'] = ".$value.";\n";
}

echo "\n";
echo "const loadersConfig = [];\n";

echo "\n";
echo "const pluginsConfig = [];\n";
/** @var \Visca\JsPackager\Webpack\Plugins\PluginDescriptorInterface $plugins */
foreach ($plugins as $plugin) {
    echo "pluginsConfig.push(new ".$plugin->name()."(".PHPEngine::jsonEncode($plugin->getOptions())."));\n";
}


echo "
module.exports = {
    entry: entriesConfig,
    output: outputConfig,
    resolve: {
        alias: aliasConfig
    },
    module: {
        loaders: loadersConfig
    },
    plugins: pluginsConfig,
    devtool: 'source-map'
};";

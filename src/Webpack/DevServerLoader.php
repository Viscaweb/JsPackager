<?php declare(strict_types=1);

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\JavascriptLoader;

/**
 * Class DevServerLoader
 * @package Visca\JsPackager\Webpack
 *
 * This Javascript loader is to be used on DEVELOPMENT environment.
 * webpack-dev-server must be running (set $serverUrl accordingly)
 * and must be configured to spit stats.json somewhere so this service
 * has access to it ($mapFilePath).
 */
class DevServerLoader implements JavascriptLoader
{
    /** @var string */
    private $mapFilePath;

    /** @var string */
    private $serverUrl;

    public function __construct(string $mapFilePath, string $serverUrl)
    {
        $this->mapFilePath = $mapFilePath;
        $this->serverUrl = $serverUrl;
    }


    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $filenames = $this->getEntryPointAssets($entryPoint, $configuration->getName());

        return implode('', array_map([$this, 'buildUrl'], $filenames));
    }

    private function getEntryPointAssets(EntryPoint $entryPoint, $context): array
    {
        $path = $this->mapFilePath.'/'.$context.'/webpack.stats.json';
        if (!file_exists($path)) {
            throw new \RuntimeException('no webpack build stats file was found: "'.$path.'". Be sure you started up webpack-dev-server');
        }

        $stats = json_decode(file_get_contents($path), true);

        if (!isset($stats['entrypoints'])) {
            throw new \RuntimeException('did not find key `entrypoints` in '.$path);
        }

        if (!isset($stats['entrypoints'][$entryPoint->getName()])) {
            throw new \RuntimeException(sprintf(
                'did not find key `entrypoints.%s` in %s',
                $entryPoint->getName(),
                $path
            ));
        }

        $assets = $stats['entrypoints'][$entryPoint->getName()]['assets'];
        $assets = array_filter($assets, function ($path) {
            return preg_match('/\.js$/', $path);
        });

        $assets = array_map(function ($path) use ($stats) {
            return $stats['publicPath'] . $path;
        }, $assets);

        return $assets;
    }

    private function buildUrl(string $filename)
    {
        return '<script src="'.$this->serverUrl.'/'.ltrim($filename, '/').'"></script>';
    }
}
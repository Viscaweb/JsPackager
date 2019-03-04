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

    private function getEntryPointAssets(EntryPoint $entryPoint, string $context): array
    {
        if (!file_exists($this->mapFilePath)) {
            throw new \RuntimeException('no webpack build stats file was found: "'.$this->mapFilePath.'". Be sure you started up webpack-dev-server');
        }

        $map = json_decode(file_get_contents($this->mapFilePath), true);

        if (!isset($map[$context])) {
            throw new \RuntimeException('did not find key `'.$context.'` in '.$this->mapFilePath);
        }

        $contextMap = $map[$context];

        if (!isset($contextMap[$entryPoint->getName()])) {
            throw new \RuntimeException(sprintf(
                'did not find key `%s.%s` in %s',
                $context,
                $entryPoint->getName(),
                $this->mapFilePath
            ));
        }

        $serverUrl = $this->serverUrl;
        $assets = $contextMap[$entryPoint->getName()];
        $assets = array_filter($assets, function ($path) {
            return preg_match('/\.js$/', $path);
        });

        $assets = array_map(function ($path) use ($serverUrl) {
            return $serverUrl .$path;
        }, $assets);

        return $assets;
    }

    private function buildUrl(string $filename)
    {
        return '<script src="'.ltrim($filename, '/').'"></script>';
    }
}
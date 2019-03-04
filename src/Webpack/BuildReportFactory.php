<?php

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Report\EntryPoint;
use Visca\JsPackager\Report\BundleReport;

class BuildReportFactory
{
    /**
     * @throws \RuntimeException
     */
    public static function create(string $webpackOutput): BundleReport
    {
        // Try to convert output to JSON
        $jsonStats = json_decode($webpackOutput, true);
        if ($jsonStats === false) {
            throw new \RuntimeException('Could not json_decode on webpack output.');
        }

        $commonAssets = [];
        $keys = array_keys($jsonStats['assetsByChunkName']);
        $vendorKeys = array_filter(
            $keys,
            function ($item) {
                //vendor;
                return (substr($item, 0, 6) === 'vendor');
            }
        );

        foreach ($vendorKeys as $key) {
            $asset = $jsonStats['assetsByChunkName'][$key];
            if (\is_array($asset)) {
                // We may have generated source-maps, webpack groups them by filename.
                if (self::isMapFile($asset[0])) {
                    continue;
                }
                $asset = $asset[0];
            }

            $commonAssets[$key] = $jsonStats['publicPath'].$asset;
        }
        ksort($commonAssets);

        if (!isset($jsonStats['entrypoints'])) {
            throw new \RuntimeException('No entrypoints found.');
        }

        $assetsBuilt = [];
        foreach ($jsonStats['entrypoints'] as $name => $data) {
            $filenames = array_map(function ($filename) use ($jsonStats) {
                return $jsonStats['publicPath'].$filename;
            }, $data['assets']);


            $filenames = array_filter($filenames, function ($filename) {
                return !self::isMapFile($filename);
            });

            $assetsBuilt[$name] = new EntryPoint($name, array_values($filenames));
        }

        $errors = [];
        if (isset($jsonStats['errors']) && \count($jsonStats['errors'])) {
            $errors[] = $jsonStats['errors'][0];
        }

        $report = new BundleReport($assetsBuilt, $commonAssets, $jsonStats['time'], $jsonStats['version'], $errors);

        return $report;
    }

    private static function isMapFile($asset)
    {
        $needle = '.js.map';
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($asset, -$length) === $needle);
    }
}

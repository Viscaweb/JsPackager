<?php

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Packager\Report\BuildReport;
use Visca\JsPackager\Packager\Report\EntryPoint;
use Visca\JsPackager\Packager\Report\Report;

class BuildReportFactory
{
    /**
     * @throws \RuntimeException
     */
    public static function create(string $webpackOutput): Report
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
                $asset = $asset[0];
            }

            $commonAssets[$key] = $jsonStats['publicPath'].$asset;
        }
        ksort($commonAssets);

        $assetsBuilt = [];
        if (isset($jsonStats['assetsByChunkName'])) {
            foreach ($jsonStats['assetsByChunkName'] as $name => $asset) {
                $path = '';
                if (\is_string($asset)) {
                    $path = $asset;
                } elseif (\is_array($asset)) {
                    // We may have generated source-maps, paths are grouped.
                    $path = $asset[0];
                }

                $urls = [];
                foreach ($commonAssets as $commonAsset) {
                    $urls[] = $commonAsset;
                }
                $urls[] = $jsonStats['publicPath'].$path;


                $assetsBuilt[$name] = new EntryPoint($name, $urls);
            }
        }

        $errors = [];
        if (isset($jsonStats['errors']) && \count($jsonStats['errors'])) {
            $errors[] = $jsonStats['errors'][0];
        }

        $report = new BuildReport($assetsBuilt, $commonAssets, $jsonStats['time'], $jsonStats['version'], $errors);

        return $report;
    }
}

<?php

namespace Visca\JsPackager\Model;

/**
 * Class PackageStats
 */
class PackageStats
{
    /** @var string[] */
    private $assetsBuilt;

    /** @var string[] */
    private $assetsVendor;

    /** @var string[] */
    private $errors;

    /**
     * PackageStats constructor.
     *
     * @param array $assetsBuilt
     * @param array $assetsVendor
     * @param array $errors
     */
    public function __construct($assetsBuilt, array $assetsVendor = [], array $errors = [])
    {
        $this->assetsBuilt = $assetsBuilt;
        $this->assetsVendor = $assetsVendor;
        $this->errors = $errors;
    }

    /**
     * @return \string[]
     */
    public function getAssetsBuilt()
    {
        return $this->assetsBuilt;
    }

    /**
     * @return \string[]
     */
    public function getVendorAssets()
    {
        return $this->assetsVendor;
    }

    /**
     * @return \string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

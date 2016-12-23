<?php

namespace Visca\JsPackager\Model;

/**
 * Class PackageStats
 */
class PackageStats
{
    /**
     * @var string[]
     */
    private $assetsBuilt;

    public function __construct($assetsBuilt)
    {
        $this->assetsBuilt = $assetsBuilt;
    }

    /**
     * @return \string[]
     */
    public function getAssetsBuilt()
    {
        return $this->assetsBuilt;
    }
}

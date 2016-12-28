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


    /** @var string[] */
    private $errors;

    /**
     * PackageStats constructor.
     *
     * @param array $assetsBuilt
     * @param array $errors
     */
    public function __construct($assetsBuilt, array $errors = [])
    {
        $this->assetsBuilt = $assetsBuilt;
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
    public function getErrors()
    {
        return $this->errors;
    }
}

<?php

namespace Visca\JsPackager\Packager\Report;

class BuildReport implements Report
{
    /** @var string */
    protected $version;

    /** @var int */
    protected $time;

    /** @var string[] */
    protected $errors;

    /** @var EntryPoint[] */
    protected $assets;

    /** @var [] */
    protected $commonAssets;

    public function __construct($assets, $commonAssets, $time, $version, $errors = [])
    {
        $this->assets = $assets;
        $this->commonAssets = $commonAssets;
        $this->time = $time;
        $this->version = $version;
        $this->errors = $errors;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return null|EntryPoint|EntryPoint[]
     */
    public function getAssets(?string $key = null)
    {
        if ($key === null) {
            return $this->assets;
        }

        return $this->assets[$key] ?? null;
    }

    public function getCommonAssets()
    {
        return $this->commonAssets;
    }
}

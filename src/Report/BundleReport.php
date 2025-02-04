<?php declare(strict_types=1);

namespace Visca\JsPackager\Report;

class BundleReport
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
     * @return EntryPoint|EntryPoint[]
     */
    public function getAssets(?string $key = null)
    {
        if ($key === null) {
            return $this->assets;
        }

        return $this->assets[$key] ?? [];
    }

    public function getCommonAssets()
    {
        return $this->commonAssets;
    }
}

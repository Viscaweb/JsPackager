<?php

namespace Visca\JsPackager\Resource;

/**
 * Class UrlAssetResource
 */
class UrlAssetResource implements AssetResource
{
    /** @var string */
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}

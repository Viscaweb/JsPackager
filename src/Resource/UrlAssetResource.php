<?php

namespace Visca\JsPackager\Resource;

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

    public function prependContent(string $content)
    {
        // Ignore on purpose
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

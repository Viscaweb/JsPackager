<?php

namespace Visca\JsPackager\Model;

/**
 * Class UrlResource
 */
class UrlResource implements Resource
{
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }
}

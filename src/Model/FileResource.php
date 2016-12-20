<?php

namespace Visca\JsPackager\Model;

/**
 * Class FileResource
 */
class FileResource implements Resource
{
    /** @var string */
    protected $path;

    /**
     * FileResource constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return file_get_contents($this->path);
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        // TODO: Implement getUrl() method.
    }
}

<?php declare(strict_types=1);

namespace Visca\JsPackager\Resource;

class FileAssetResource implements AssetResource
{
    /** @var string */
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getContent(): string
    {
        return file_get_contents($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return 'string://memory';
    }
}

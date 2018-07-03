<?php declare(strict_types=1);

namespace Visca\JsPackager\Resource;

class StringAssetResource implements AssetResource
{
    /** @var string */
    protected $content;

    /** @var string */
    protected $temporalPath;

    /**
     * StringAssetResource constructor.
     *
     * @param string $content
     * @param string $temporalPath In case we need the filepath to this resource we need to
     *                             specify what temporal path we want to store this file.
     *                             specify the full filename too.
     */
    public function __construct(string $content, string $temporalPath)
    {
        $this->content = $content;
        $this->temporalPath = $temporalPath;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPath(): string
    {
        $result = file_put_contents($this->temporalPath, $this->content);
        if ($result === false) {
            throw new \RuntimeException('Could not save temporal file for StringAssetResource at "'.$this->temporalPath.'"');
        }

        return $this->temporalPath;
    }

    public function getUrl(): string
    {
        return 'string://memory';
    }
}

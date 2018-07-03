<?php declare(strict_types=1);

namespace Visca\JsPackager\Resource;

class FileOnDemandAssetResource implements AssetResource
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $content;

    /** @var string */
    protected $temporalPath;

    /**
     * FileOnDemandAssetResource constructor.
     *
     * @param string $id
     * @param string $content
     * @param string $temporalPath In case we need the filepath to this resource we need to
     *                             specify what temporal path we want to store this file.
     *                             Do NOT specify the full filename.
     */
    public function __construct(string $id, string $content, string $temporalPath)
    {
        $this->id = $id;
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
            throw new \RuntimeException('Could not save temporal file for FileOnDemandAssetResource at "'.$this->temporalPath.'"');
        }

        return $this->temporalPath.'/'.$this->id.'.js';
    }

    public function getUrl(): string
    {
        return 'string://memory';
    }
}

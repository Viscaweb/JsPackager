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

    public function prependContent(string $content)
    {
        $this->content = $content.$content;
    }

    public function getPath(): string
    {
        if (!is_dir($this->temporalPath)) {
            mkdir($this->temporalPath, 0777, true);
        }

        $path = $this->temporalPath.'/'.$this->id.'.js';
        $result = file_put_contents($path, $this->content);
        if ($result === false) {
            throw new \RuntimeException('Could not save temporal file for FileOnDemandAssetResource at "'.$path.'"');
        }

        return $path;
    }

    public function getUrl(): string
    {
        return 'string://memory';
    }
}

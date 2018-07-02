<?php

declare(strict_types=1);

namespace Visca\JsPackager\Webpack;

use Visca\JsPackager\Configuration\Alias;
use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Resource\FileAssetResource;
use Visca\JsPackager\Webpack\Plugins\PluginDescriptorInterface;

class WebpackConfiguration
{
    /** @var string */
    protected $outputPath;

    /** @var string */
    protected $outputPublicPath;

    /** @var EntryPoint[] */
    protected $entryPoints;

    /** @var Alias[] */
    protected $aliases;

    /** @var PluginDescriptorInterface[] */
    protected $plugins;

    /**
     * @throws \RuntimeException
     */
    public function __construct(string $outputPath, array $entryPoints, array $aliases, array $plugins = [])
    {
        $this->outputPath = $outputPath;

        $sanitizedEntryPoints = [];
        /**
         * @var EntryPoint[] $entryPoints
         */
        foreach ($entryPoints as $entryPoint) {
            if ($entryPoint->getResource() instanceof FileAssetResource === false) {
                throw new \RuntimeException(
                    sprintf('Entry point "%s" has resource of type "%s". This is not compatible with Webpack. Use solely FileAssetResource types.',
                        $entryPoint->getName(),
                        \get_class($entryPoint->getResource())
                    )
                );
            }
        }
        $this->entryPoints = $entryPoints;
        $this->aliases = $aliases;
        $this->plugins = $plugins;
    }

    /**
     * @param string $outputPublicPath
     *
     * @return WebpackConfiguration
     */
    public function setOutputPublicPath(?string $outputPublicPath = null)
    {
        $this->outputPublicPath = $outputPublicPath;

        return $this;
    }

    public function outputPath(): string
    {
        return $this->outputPath;
    }

    public function outputPublicPath(): ?string
    {
        return $this->outputPublicPath;
    }

    /**
     * @return EntryPoint[]
     */
    public function entryPoints(): array
    {
        return $this->entryPoints;
    }

    /**
     * @return Alias[]
     */
    public function aliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return PluginDescriptorInterface[]
     */
    public function plugins(): array
    {
        return $this->plugins;
    }

    public function modules(): array
    {
        $included = [];
        $jsModules = [];
        foreach ($this->plugins as $plugin) {
            $moduleName = $plugin->getModuleName();
            if ($moduleName !== null && !in_array($moduleName, $included)) {
                $jsModules[] = $plugin->getRequireCall();
                $included[] = $moduleName;
            }
        }

        return $jsModules;
    }
}

<?php

namespace Visca\JsPackager\Configuration;

/**
 * Class ConfigurationDefinition
 */
class ConfigurationDefinition
{
    /** @var string */
    private $name;

    /** @var string */
    private $currentEnvironment;

    /**
     * @var string Specified the public URL address of the output
     *             files when referenced in a browser.
     */
    private $outputPublicPath;

    /**
     * @var string Path from where to run the build programs. Usually `%kernel.root_dir%/../`
     *             This is useful so compiler finds ./node_modules/.bin
     */
    private $workingPath;

    /** @var string Where to store output files */
    private $buildOutputPath;

    /** @var string[] Domains to use in the paths of assets */
    private $domainsInjection;

    /** @var string Environment in which we want to inject the domains */
    private $domainsInjectionEnvironment;

    /** @var EntryPoint[] */
    private $entryPoints = [];

    /** @var array */
    private $globalInline = [];

    /** @var array */
    private $globalInclude = [];

    /** @var Alias[] */
    private $alias = [];

    /** @var bool */
    private $minifyEnabled;

    public function __construct(string $name, string $environment, string $workingPath)
    {
        $this->name = $name;
        $this->minifyEnabled = true;
        $this->currentEnvironment = $environment;
        $this->workingPath = $workingPath;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCurrentEnvironment()
    {
        return $this->currentEnvironment;
    }

    /**
     * @return string
     */
    public function getOutputPublicPath(): ?string
    {
        if ($this->outputPublicPath === null) {
            return null;
        }

        return rtrim($this->outputPublicPath, '/').'/';
    }

    /**
     * @param string $outputPublicPath
     *
     * @return ConfigurationDefinition
     */
    public function setOutputPublicPath($outputPublicPath): self
    {
        $this->outputPublicPath = rtrim($outputPublicPath, '/').'/';

        return $this;
    }

    /**
     * @return string
     */
    public function getBuildOutputPath()
    {
        return $this->buildOutputPath;
    }

    /**
     * @param string $buildOutputPath
     *
     * @return ConfigurationDefinition
     */
    public function setBuildOutputPath($buildOutputPath)
    {
        $this->buildOutputPath = $buildOutputPath;

        return $this;
    }

    /**
     * @param       $environment
     * @param array $domains
     *
     * @return $this
     */
    public function setDomainsInjection($environment, $domains)
    {
        $this->domainsInjectionEnvironment = $environment;
        $this->domainsInjection = $domains;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDomainsInjection()
    {
        return $this->domainsInjection;
    }

    /**
     * Adds a new entry point to be processed.
     *
     * @param EntryPoint $entryPoint
     */
    public function addEntryPoint(EntryPoint $entryPoint)
    {
        $this->entryPoints[] = $entryPoint;
    }

    /**
     * @return EntryPoint[]
     */
    public function getEntryPoints(): array
    {
        return $this->entryPoints;
    }

    public function addAlias(Alias $alias)
    {
        $this->alias[] = $alias;
    }

    /**
     * @return Alias[]
     */
    public function getAlias(): array
    {
        return $this->alias;
    }

    /**
     * Returns a list of url scripts to be included in the page
     * with
     *
     * <script src="<url>"></script>
     *
     * @return EntryPoint[]
     */
    public function getEntryPointsGlobalIncludes(): array
    {
        return $this->globalInclude;
    }

    public function setEntryPointsGlobalIncludes(array $globalIncludes = []): self
    {
        $this->globalInclude = $globalIncludes;

        return $this;
    }

    /**
     * Returns a list of Javascript paths that is intended to be appended
     * inline into the designed entry point.
     *
     * @return EntryPoint[]
     */
    public function getEntryPointsGlobalInline(): array
    {
        return $this->globalInline;
    }

    public function setEntryPointsGlobalInline(array $globalInline = []): self
    {
        $this->globalInline = $globalInline;

        return $this;
    }

    public function getDomainInjectionEnvironment(): ?string
    {
        return $this->domainsInjectionEnvironment;
    }

    public function isMinifyEnabled(): bool
    {
        return $this->minifyEnabled;
    }

    public function setMinifyEnabled(bool $minifyEnabled): self
    {
        $this->minifyEnabled = $minifyEnabled;

        return $this;
    }

    public function getWorkingPath(): string
    {
        return $this->workingPath;
    }
}

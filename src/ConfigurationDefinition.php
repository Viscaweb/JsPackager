<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\EntryPointInterface;
use Visca\JsPackager\Configuration\ResourceJs as ResourceAlias;

/**
 * Class ConfigurationDefinition
 */
class ConfigurationDefinition
{
    /** @var string */
    private $currentEnvironment;

    /**
     * @var string Specified the public URL address of the output
     *             files when referenced in a browser.
     */
    private $outputPublicPath;

    /** @var string Where to store output files */
    private $buildOutputPath;

    /** @var string[] Domains to use in the paths of assets */
    private $domainsInjection;

    /** @var string Environment in which we want to inject the domains */
    private $domainsInjectionEnvironment;

    /** @var EntryPointInterface[] */
    private $entryPoints = [];

    /** @var array */
    private $globalInline = [];

    /** @var array */
    private $globalInclude = [];

    /** @var ResourceAlias[] */
    private $alias = [];

    /**
     * ConfigurationDefinition constructor.
     *
     * @param string $environment
     */
    public function __construct($environment)
    {
        $this->currentEnvironment = $environment;
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
    public function getOutputPublicPath()
    {
        return $this->outputPublicPath;
    }

    /**
     * @param string $outputPublicPath
     *
     * @return ConfigurationDefinition
     */
    public function setOutputPublicPath($outputPublicPath)
    {
        $this->outputPublicPath = $outputPublicPath;

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
     * @param EntryPointInterface $entryPoint
     */
    public function addEntryPoint(EntryPointInterface $entryPoint)
    {
        $this->entryPoints[] = $entryPoint;
    }

    /**
     * @return EntryPointInterface[]
     */
    public function getEntryPoints()
    {
        return $this->entryPoints;
    }

    /**
     * @param ResourceAlias $alias
     */
    public function addAlias(ResourceAlias $alias)
    {
        $this->alias[] = $alias;
    }

    /**
     * @return ResourceAlias[]
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Returns a list of url scripts to be included in the page
     * with
     *
     * <script src="<url>"></script>
     *
     * @return array
     */
    public function getEntryPointsGlobalIncludes()
    {
        return $this->globalInclude;
    }

    /**
     * @param array $globalIncludes
     *
     * @return $this
     */
    public function setEntryPointsGlobalIncludes($globalIncludes)
    {
        $this->globalInclude = $globalIncludes;

        return $this;
    }

    /**
     * Returns a list of Javascript paths that is intended to be appended
     * inline into the designed entry point.
     *
     * @return array
     */
    public function getEntryPointsGlobalInline()
    {
        return $this->globalInline;
    }

    /**
     * @param array $globalInline
     *
     * @return $this
     */
    public function setEntryPointsGlobalInline($globalInline)
    {
        $this->globalInline = $globalInline;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainInjectionEnvironment()
    {
        return $this->domainsInjectionEnvironment;
    }
}

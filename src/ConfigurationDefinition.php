<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Model\EntryPoint;
use Visca\JsPackager\Model\Alias;

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

    /**
     * ConfigurationDefinition constructor.
     *
     * @param string $name
     * @param string $environment
     */
    public function __construct($name, $environment)
    {
        $this->name = $name;
        $this->minifyEnabled = true;
        $this->currentEnvironment = $environment;
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
    public function getEntryPoints()
    {
        return $this->entryPoints;
    }

    /**
     * @param Alias $alias
     */
    public function addAlias(Alias $alias)
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
     * @return EntryPoint[]
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
     * @return EntryPoint[]
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

    /**
     * @return bool
     */
    public function isMinifyEnabled()
    {
        return $this->minifyEnabled;
    }

    /**
     * @param bool $minifyEnabled
     *
     * @return $this
     */
    public function setMinifyEnabled($minifyEnabled)
    {
        $this->minifyEnabled = $minifyEnabled;

        return $this;
    }
}

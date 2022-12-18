<?php

namespace Salah3id\Domains\Repository\Generators;

/**
 * Class CriteriaGenerator
 * @package Salah3id\Domains\Repository\Generators
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class ModelRelationsGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'model/relation';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'relations';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Relations.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->domainPath;
    }
}

<?php
namespace Salah3id\Domains\Repository\Generators;

/**
 * Class PresenterGenerator
 * @package Salah3id\Domains\Repository\Generators
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class PresenterGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'presenter/presenter';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode()) . '\\' . $this->getName();
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'presenters';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $transformerGenerator = new TransformerGenerator([
            'name' => $this->name
        ],$this->domain,$this->domainPath);
        $transformer = $transformerGenerator->getRootNamespace() . '\\' .$transformerGenerator->getName().'\\'. $transformerGenerator->getName() . 'Resource';
        $transformer = str_replace([
            "\\",
            '/'
        ], '\\', $transformer);
        echo $transformer;

        return array_merge(parent::getReplacements(), [
            'transformer' => $transformer
        ]);
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) .'/'. $this->getName(). '/' . $this->getName() . 'Collection.php';
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

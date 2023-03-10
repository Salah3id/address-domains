<?php

namespace Salah3id\Domains\Repository\Generators;

use Salah3id\Domains\Repository\Generators\Migrations\SchemaParser;

/**
 * Class ModelGenerator
 * @package Salah3id\Domains\Repository\Generators
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class ModelGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'model';

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
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'models';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . '.php';
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

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $relations = parent::getRootNamespace() . parent::getConfigGeneratorClassPath('relations') . '\\' . ucfirst($this->name) . 'Relations;';
        $relations = str_replace([
            "\\",
            '/'
        ], '\\', $relations);
        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable(),
            'relations' => $relations,
            'use_relations' => ucfirst($this->name).'Relations'
        ]);
    }

    /**
     * Get the fillable attributes.
     *
     * @return string
     */
    public function getFillable()
    {
        if (!$this->fillable) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t\t'{$column}'," . PHP_EOL;
        }

        return $results . "\t" . ']';
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser()
    {
        return new SchemaParser($this->fillable);
    }
}

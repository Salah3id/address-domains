<?php
namespace Salah3id\Domains\Repository\Generators;

use Salah3id\Domains\Repository\Generators\Migrations\RulesParser;
use Salah3id\Domains\Repository\Generators\Migrations\SchemaParser;

/**
 * Class ValidatorGenerator
 * @package Salah3id\Domains\Repository\Generators
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class ValidatorGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'validator/validator';

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
        return 'validators';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Validator.php';
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

        return array_merge(parent::getReplacements(), [
            'rules' => $this->getRules(),
        ]);
    }

    /**
     * Get the rules.
     *
     * @return string
     */
    public function getRules()
    {
        if (!$this->rules) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t\t'{$column}'\t=>'\t{$value}'," . PHP_EOL;
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
        return new RulesParser($this->rules);
    }
}

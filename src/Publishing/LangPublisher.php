<?php

namespace Salah3id\Domains\Publishing;

use Salah3id\Domains\Support\Config\GenerateConfigReader;

class LangPublisher extends Publisher
{
    /**
     * Determine whether the result message will shown in the console.
     *
     * @var bool
     */
    protected $showMessage = false;

    /**
     * Get destination path.
     *
     * @return string
     */
    public function getDestinationPath()
    {
        $name = $this->domain->getLowerName();

        return base_path("resources/lang/{$name}");
    }

    /**
     * Get source path.
     *
     * @return string
     */
    public function getSourcePath()
    {
        return $this->getDomain()->getExtraPath(
            GenerateConfigReader::read('lang')->getPath()
        );
    }
}

<?php

namespace Salah3id\Domains\Publishing;

use Salah3id\Domains\Support\Config\GenerateConfigReader;

class AssetPublisher extends Publisher
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
        return $this->repository->assetPath($this->domain->getLowerName());
    }

    /**
     * Get source path.
     *
     * @return string
     */
    public function getSourcePath()
    {
        return $this->getDomain()->getExtraPath(
            GenerateConfigReader::read('assets')->getPath()
        );
    }
}

<?php

namespace Salah3id\Domains\Process;

use Salah3id\Domains\Contracts\RepositoryInterface;
use Salah3id\Domains\Contracts\RunableInterface;

class Runner implements RunableInterface
{
    /**
     * The domain instance.
     * @var RepositoryInterface
     */
    protected $domain;

    public function __construct(RepositoryInterface $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Run the given command.
     *
     * @param string $command
     */
    public function run($command)
    {
        passthru($command);
    }
}

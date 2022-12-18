<?php

namespace Salah3id\Domains\Traits;

use Symfony\Component\Console\Question\ChoiceQuestion;

trait DomainCommandTrait
{
    /**
     * Get the domain name.
     *
     * @return string
     */
    public function getDomainName()
    {
        $domain = $this->argument('domain') ?: app('domains')->getUsedNow();

        $domain = app('domains')->findOrFail($domain);

        return $domain->getStudlyName();
    }

    /**
     * Get the domain name for Repository Commands.
     *
     * @return string
     */
    public function getDomainNameForRepo()
    {
        try {
            $domain = app('domains')->getUsedNow();

            $domain = app('domains')->findOrFail($domain)->getStudlyName();

        } catch (\Throwable $th) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Select Domain :',
                array_values($this->laravel['domains']->all()),
                0
            );
            $question->setErrorMessage('Domain %s is invalid.');
            $domain = $helper->ask($this->input, $this->output, $question);;
        }
        return $domain;
    }
}

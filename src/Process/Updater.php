<?php

namespace Salah3id\Domains\Process;

use Salah3id\Domains\Domain;

class Updater extends Runner
{
    /**
     * Update the dependencies for the specified domain by given the domain name.
     *
     * @param string $domain
     */
    public function update($domain)
    {
        $domain = $this->domain->findOrFail($domain);

        chdir(base_path());

        $this->installRequires($domain);
        $this->installDevRequires($domain);
        $this->copyScriptsToMainComposerJson($domain);
    }

    /**
     * Check if composer should output anything.
     *
     * @return string
     */
    private function isComposerSilenced()
    {
        return config('domains.composer.composer-output') === false ? ' --quiet' : '';
    }

    /**
     * @param Domain $domain
     */
    private function installRequires(Domain $domain)
    {
        $packages = $domain->getComposerAttr('require', []);

        $concatenatedPackages = '';
        foreach ($packages as $name => $version) {
            $concatenatedPackages .= "\"{$name}:{$version}\" ";
        }

        if (!empty($concatenatedPackages)) {
            $this->run("composer require {$concatenatedPackages}{$this->isComposerSilenced()}");
        }
    }

    /**
     * @param Domain $domain
     */
    private function installDevRequires(Domain $domain)
    {
        $devPackages = $domain->getComposerAttr('require-dev', []);

        $concatenatedPackages = '';
        foreach ($devPackages as $name => $version) {
            $concatenatedPackages .= "\"{$name}:{$version}\" ";
        }

        if (!empty($concatenatedPackages)) {
            $this->run("composer require --dev {$concatenatedPackages}{$this->isComposerSilenced()}");
        }
    }

    /**
     * @param Domain $domain
     */
    private function copyScriptsToMainComposerJson(Domain $domain)
    {
        $scripts = $domain->getComposerAttr('scripts', []);

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        foreach ($scripts as $key => $script) {
            if (array_key_exists($key, $composer['scripts'])) {
                $composer['scripts'][$key] = array_unique(array_merge($composer['scripts'][$key], $script));

                continue;
            }
            $composer['scripts'] = array_merge($composer['scripts'], [$key => $script]);
        }

        file_put_contents(base_path('composer.json'), json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}

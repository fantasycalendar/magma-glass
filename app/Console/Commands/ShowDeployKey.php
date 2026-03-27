<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ShowDeployKey extends Command
{
    protected $signature = 'github:show-key';

    protected $description = 'Displays the public deploy key for adding to Github.';

    public function handle(): int
    {
        $source = config('magmaglass.github_deploy_key');

        if ($source !== null) {
            $this->info('Key is passed in via GITHUB_DEPLOY_KEY env variable.');
            $this->warn('Cannot display inline key contents for security reasons.');

            return 0;
        }

        $source = config('magmaglass.github_deploy_key_path');
        $keyPath = Str::startsWith($source, '/')
            ? $source
            : storage_path($source);

        $publicKeyPath = $keyPath . '.pub';

        if (file_exists($publicKeyPath)) {
            $this->info('Public key (add this to GitHub as a deploy key):');
            $this->newLine();
            $this->line(file_get_contents($publicKeyPath));

            return 0;
        }

        $this->error("Public key not found at: $publicKeyPath");
        $this->error('Has the entrypoint generated a deploy key yet?');

        return 1;
    }
}

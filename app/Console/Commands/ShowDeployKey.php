<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ShowDeployKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:show-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays the path and contents of the deploy key being used to retrieve notes from Github.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $source = config('magmaglass.github_deploy_key');

        if($source !== null) {
            $this->info("Key is passed in via GITHUB_DEPLOY_KEY env variable.");
            $this->info("Key contents:");
            $this->info($source);

            return 0;
        }

        $source = config('magmaglass.github_deploy_key_path');
        $keyPath = Str::startsWith($source, '/')
            ? $source
            : storage_path($source);

        if(file_exists($keyPath)) {
            $this->info("Key is a file, sourced from path:");
            $this->info($source);
            $this->newLine();

            $this->info("Private key:");
            $this->info(file_get_contents($keyPath));

            $this->info("Public key:");
            $this->info(file_get_contents($keyPath.".pub"));

            return 0;
        }

        $this->error("Key is expected to be a file sourced from path:");
        $this->error($keyPath);
        $this->newLine();
        $this->error("However, the file does not appear to exist!");

        return 0;
    }
}

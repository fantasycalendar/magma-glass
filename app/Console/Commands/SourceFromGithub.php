<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\GitWrapper;

class SourceFromGithub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:source-latest
                            {--silent= : Run silently, suppressing output.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets latest source from Github';

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
        // Ease of access
        $githubRepo = config('magmaglass.github_repo');
        $repoSubdir = config('magmaglass.github_repo_subdir');
        $nowPrefix = now()->format('Y-m-d-H-i-s');
        $cloneTo = Storage::disk('gittemp')->path($nowPrefix);
        $path = Str::startsWith(config('magmaglass.github_deploy_key_path'), '/')
            ? config('magmaglass.github_deploy_key_path')
            : storage_path(config('magmaglass.github_deploy_key_path'));

        // We're using GitWrapper to simplify the process of checking out the git repo.
        // It's just using Symfony/Process under the hood, but does some extra work for us.
        $gitWrapper = new GitWrapper('git');
        $gitWrapper->setPrivateKey($path);

        if(!$this->option('silent')) {
            $this->info("Cloning $githubRepo to $cloneTo");
        }

        // If it fails, we should get an exception thrown, and we won't go through the process
        // of deleting/swapping/moving things. That's important as we don't want to break stuff.
        try {
            if(!$this->option('silent')) {
                $gitWrapper->streamOutput();
            }

            $gitWrapper->cloneRepository($githubRepo, $cloneTo);
        } catch (\Throwable $e) {
            if(!$this->option('silent')) {
                $this->error("Welp, we tried. Errors should be above.");
            }
        }

        if(!in_array($nowPrefix . $repoSubdir, Storage::disk('gittemp')->allDirectories())) {
            $this->error("Did not find subdir $repoSubdir in $cloneTo. Exiting.");

            return 1;
        }

        // Do the file things!
        $this->emptyArticleRoot();

        $articlesDisk = Storage::disk('articles');
        $disk = Storage::build([
            'driver' => 'local',
            'root' => $cloneTo . $repoSubdir
        ]);

        foreach($disk->allDirectories() as $directory) {
            File::moveDirectory($disk->path($directory), $articlesDisk->path($directory));
        }

        foreach($disk->allFiles() as $file) {
            File::move($disk->path($file), $articlesDisk->path($file));
        }

        dump(Storage::disk('articles')->allDirectories());

        return 0;
    }

    private function emptyArticleRoot()
    {
        foreach(Storage::disk('articles')->directories() as $directory) {
            Storage::disk('articles')->delete($directory);
        }

        foreach(Storage::disk('articles')->files() as $file) {
            Storage::disk('articles')->delete($file);
        }
    }
}

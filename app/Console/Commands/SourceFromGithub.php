<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SourceFromGithub extends Command
{
    protected $signature = 'github:source-latest
                            {--silent : Run silently, suppressing output.}';

    protected $description = 'Gets latest source from Github';

    public function handle(): int
    {
        $githubRepo = config('magmaglass.github_repo');
        $repoSubdir = config('magmaglass.github_repo_subdir');
        $nowPrefix = now()->format('Y-m-d-H-i-s');
        $cloneTo = Storage::disk('gittemp')->path($nowPrefix);
        $keyPath = Str::startsWith(config('magmaglass.github_deploy_key_path'), '/')
            ? config('magmaglass.github_deploy_key_path')
            : storage_path(config('magmaglass.github_deploy_key_path'));

        if (! $this->option('silent')) {
            $this->info("Cloning $githubRepo to $cloneTo");
        }

        // Use GIT_SSH_COMMAND to specify the deploy key
        $sshCommand = sprintf('ssh -i %s -o StrictHostKeyChecking=accept-new', escapeshellarg($keyPath));

        $process = new Process(
            ['git', 'clone', '--depth', '1', $githubRepo, $cloneTo],
            null,
            ['GIT_SSH_COMMAND' => $sshCommand]
        );

        $process->setTimeout(300);

        try {
            if (! $this->option('silent')) {
                $process->run(function ($type, $buffer) {
                    $this->output->write($buffer);
                });
            } else {
                $process->run();
            }

            if (! $process->isSuccessful()) {
                $this->error('Failed to clone repository: ' . $process->getErrorOutput());

                return 1;
            }
        } catch (\Throwable $e) {
            $this->error('Failed to clone repository: ' . $e->getMessage());

            return 1;
        }

        if (! in_array($nowPrefix . $repoSubdir, Storage::disk('gittemp')->allDirectories())) {
            $this->error("Did not find subdir $repoSubdir in $cloneTo. Exiting.");

            return 1;
        }

        // Only clear articles after a successful clone
        $this->emptyArticleRoot();

        $articlesDisk = Storage::disk('articles');
        $disk = Storage::build([
            'driver' => 'local',
            'root' => $cloneTo . $repoSubdir,
        ]);

        foreach ($disk->allDirectories() as $directory) {
            File::moveDirectory($disk->path($directory), $articlesDisk->path($directory));
        }

        foreach ($disk->allFiles() as $file) {
            $targetPath = $articlesDisk->path($file);
            $targetDir = dirname($targetPath);
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            File::move($disk->path($file), $targetPath);
        }

        cache()->forget('articles_cache');
        cache()->forget('file_tree');

        if (! $this->option('silent')) {
            $this->info('Articles updated successfully.');
        }

        return 0;
    }

    private function emptyArticleRoot(): void
    {
        foreach (Storage::disk('articles')->directories() as $directory) {
            Storage::disk('articles')->deleteDirectory($directory);
        }

        foreach (Storage::disk('articles')->files() as $file) {
            Storage::disk('articles')->delete($file);
        }
    }
}

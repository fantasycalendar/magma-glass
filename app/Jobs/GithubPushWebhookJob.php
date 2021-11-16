<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;

class GithubPushWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public GitHubWebhookCall $gitHubWebhookCall;
    public GitHubWebhookCall $webhookCall;

    public function __construct(GitHubWebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        // We got a new push webhook! Go clone the latest, stat!

        Artisan::call('github:source-latest', [
            '--silent' => 1
        ]);
    }
}

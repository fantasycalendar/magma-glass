<?php

use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;
use Spatie\GitHubWebhooks\Jobs\ProcessGitHubWebhookJob;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

return [
    'signing_secret' => env('GITHUB_WEBHOOK_SECRET'),

    'jobs' => [
        '*' => \App\Jobs\GithubPushWebhookJob::class,
    ],

    'model' => GitHubWebhookCall::class,

    'prune_webhook_calls_after_days' => 30,

    'job' => ProcessGitHubWebhookJob::class,

    'profile' => ProcessEverythingWebhookProfile::class,

    'verify_signature' => env('GITHUB_SIGNATURE_VERIFY', true),
];

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;

class WebhookStubModel extends GitHubWebhookCall
{
    use HasFactory;

    public static function storeWebhook(WebhookConfig $config, Request $request): WebhookCall
    {
        $headers = self::headersToStore($config, $request);

        return new self([
            'name' => $config->name,
            'url' => $request->fullUrl(),
            'headers' => $headers,
            'payload' => $request->input(),
        ]);
    }

    public function saveException(Exception $exception): self
    {
        logger()->error($exception->getCode());
        logger()->error($exception->getMessage());
        logger()->error($exception->getTraceAsString());
        
        return $this;
    }
}

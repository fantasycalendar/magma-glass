<?php

return [
    'cache_ttl' => env('ARTICLE_CACHE_TTL', 3600),
    'ignored_paths' => explode(',', env('IGNORED_PATHS', '')),

    'default_article_path' => env('DEFAULT_ARTICLE_PATH'),

    'github_branches' => env('GITHUB_WATCH_BRANCHES', 'master'),
    'github_repo' => env('GITHUB_REPO'),
    'github_deploy_key' => env('GITHUB_DEPLOY_KEY'),
    'github_repo_subdir' => env('GITHUB_REPO_SUBDIR', ''),

    'github_deploy_key_path' => env('GITHUB_DEPLOY_KEY_PATH', 'app/id_rsa'),
];

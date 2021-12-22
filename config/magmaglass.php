<?php

return [
    'cache_ttl' => env('ARTICLE_CACHE_TTL', 1),
    'ignored_paths' => explode(',', env('IGNORED_PATHS', '')),

    'github_branches' => env('GITHUB_WATCH_BRANCHES', 'master'),
    'github_repo' => env('GITHUB_REPO', null),
    'github_deploy_key' => env('GITHUB_DEPLOY_KEY', null),
    'github_repo_subdir' => env('GITHUB_REPO_SUBDIR', ''),

    'github_deploy_key_path' => env('GITHUB_DEPLOY_KEY_PATH', 'app/id_rsa')
];

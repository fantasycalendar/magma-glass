<?php

namespace App\Models;

class ArticleBlock
{
    public string $contents;
    public string $id;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
        $this->id = $this->resolveId();
    }

    private function resolveId()
    {
        if(preg_match('/(\^[A-Za-z0-9]+)$/', $this->contents, $matches)){
            return str_replace('^', '', $matches[0]);
        }

        return substr(sha1($this->contents), 0, 6);
    }
}

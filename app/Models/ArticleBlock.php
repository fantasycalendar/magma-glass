<?php

namespace App\Models;

use Illuminate\Support\Str;

class ArticleBlock
{
    public string $content;
    public string $id;

    public function __construct(string $contents)
    {
        $this->content = $contents;
        $this->id = $this->resolveId();
    }

    private function resolveId()
    {
        if(preg_match('/(\^[A-Za-z0-9]+)$/', $this->content, $matches)){
            return str_replace('^', '', $matches[0]);
        }

        return substr(sha1($this->content), 0, 6);
    }

    public function isCodeBlock()
    {
        return Str::startsWith($this->content, '```') && Str::endsWith($this->content, '```');
    }

    public function setContent(?string $content): ArticleBlock
    {
        $this->content = $content;

        return $this;
    }
}

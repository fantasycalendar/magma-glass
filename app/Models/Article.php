<?php

namespace App\Models;

use App\Services\ArticleParser;

class Article
{
    public string $contents;
    public string $name;

    public function __construct($name, $contents)
    {
        $this->name = $name;
        $this->contents = $contents;
    }

    public function getParsed(): string
    {
        return ArticleParser::parse($this);
    }
}

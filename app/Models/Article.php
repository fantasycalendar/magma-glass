<?php

namespace App\Models;

use App\Services\ArticleParser;

class Article
{
    public string $contents;
    public string $name;
    public string $path;

    public function __construct($name, $path, $contents)
    {
        $this->name = $name;
        $this->contents = $contents;
        $this->path = $path;
    }

    /**
     * Determine whether the name of this article is also its heading
     *
     * @return bool
     */
    public function nameIsHeading(): bool
    {
        return substr($this->contents, strpos($this->contents, "\n")) == '# ' . $this->name;
    }

    /**
     * Removes the first line of the content
     *
     * @return $this
     */
    public function hideFirstLine(): self
    {
        $this->contents = substr($this->contents, strpos($this->contents, "\n") + 1);

        return $this;
    }

    /**
     * Gets the parsed content of this article
     *
     * @return string
     */
    public function getParsed(): string
    {
        if($this->nameIsHeading()) {
            $this->hideFirstLine();
        }

        return ArticleParser::parse($this);
    }
}

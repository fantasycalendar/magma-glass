<?php

namespace App\Models;

use App\Services\ArticleParser\ArticlePipeline;
use Illuminate\Support\Collection;

class Article
{
    public string $content;
    public string $name;
    public string $path;
    public Collection $tags;
    private Collection $frontMatter;

    public function __construct(string $name, string $path, string $contents, array|Collection $tags = [])
    {
        $this->name = $name;
        $this->content = $contents;
        $this->path = $path;
        $this->tags = collect($tags);
        $this->frontMatter = collect();
    }

    /**
     * Determine whether the first line of content is a heading matching the article name.
     */
    public function nameIsHeading(): bool
    {
        $firstNewline = strpos($this->content, "\n");
        $firstLine = $firstNewline !== false
            ? substr($this->content, 0, $firstNewline)
            : $this->content;

        return trim($firstLine) === '# ' . $this->name;
    }

    /**
     * Removes the first line of the content.
     */
    public function hideFirstLine(): self
    {
        $firstNewline = strpos($this->content, "\n");
        if ($firstNewline !== false) {
            $this->content = substr($this->content, $firstNewline + 1);
        } else {
            $this->content = '';
        }

        return $this;
    }

    /**
     * Gets the parsed (HTML) content of this article.
     */
    public function getParsed(): string
    {
        if ($this->nameIsHeading()) {
            $this->hideFirstLine();
        }

        return ArticlePipeline::process($this)->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setFrontMatter(array $frontMatter): self
    {
        $this->frontMatter = collect($frontMatter);

        return $this;
    }

    public function getFrontMatter(): Collection
    {
        return $this->frontMatter;
    }
}

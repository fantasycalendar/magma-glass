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
     * Determine whether the first non-front-matter line of content is a heading matching the article name.
     */
    public function nameIsHeading(): bool
    {
        $content = $this->contentWithoutFrontMatter();

        $firstNewline = strpos($content, "\n");
        $firstLine = $firstNewline !== false
            ? substr($content, 0, $firstNewline)
            : $content;

        return trim($firstLine) === '# '.$this->name;
    }

    /**
     * Strip the YAML front matter block (---...---) from the beginning of content.
     */
    private function contentWithoutFrontMatter(): string
    {
        $content = $this->content;

        if (! str_starts_with(trim($content), '---')) {
            return $content;
        }

        // Find the closing --- after the opening one
        $start = strpos($content, '---');
        $end = strpos($content, '---', $start + 3);

        if ($end === false) {
            return $content;
        }

        return ltrim(substr($content, $end + 3));
    }

    /**
     * Removes the heading line that matches the article name from the content.
     * Handles both raw content (with front matter) and plain content.
     */
    public function stripNameHeading(): self
    {
        $headingLine = '# '.$this->name;

        // Replace the first occurrence of the heading line
        $this->content = preg_replace(
            '/^'.preg_quote($headingLine, '/').'\s*$/m',
            '',
            $this->content,
            1
        );

        return $this;
    }

    /**
     * Gets the parsed (HTML) content of this article.
     */
    public function getParsed(): string
    {
        if ($this->nameIsHeading()) {
            $this->stripNameHeading();
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

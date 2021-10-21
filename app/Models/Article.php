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
    public Collection $blocks;

    public function __construct($name, $path, $contents, $tags = [])
    {
        $this->name = $name;
        $this->content = $contents;
        $this->path = $path;
        $this->tags = collect($tags);
        $this->blocks = $this->blockify();
    }

    /**
     * Determine whether the name of this article is also its heading
     *
     * @return bool
     */
    public function nameIsHeading(): bool
    {
        return substr($this->content, strpos($this->content, "\n")) == '# ' . $this->name;
    }

    public function getBlocks(): Collection
    {
        return $this->blockify()->keyBy('id');
    }

    /**
     * Removes the first line of the content
     *
     * @return $this
     */
    public function hideFirstLine(): self
    {
        $this->content = substr($this->content, strpos($this->content, "\n") + 1);

        return $this;
    }

    /**
     * Gets the parsed content of this article
     *
     * @return string
     */
    public function getParsed()
    {
        if($this->nameIsHeading()) {
            $this->hideFirstLine();
        }

        return ArticlePipeline::process($this)
            ->blocks
            ->map
            ->content
            ->join("\n\n");
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    private function blockify(): Collection
    {
        return collect(preg_split('/\n{2}+/', $this->content))->mapInto(ArticleBlock::class);
    }
}

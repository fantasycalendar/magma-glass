<?php

namespace App\Services\ArticleParser\InlineParser;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class ArticleTagParser implements \League\CommonMark\Parser\Inline\InlineParserInterface
{

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('(#+[a-zA-Z0-9\-(_)]{1,})');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        // The # symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // This seems to be a valid match
        // Advance the cursor to the end of the match
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        // Grab the Tag
        [$tag] = $inlineContext->getSubMatches();
        $tag = substr($tag, 1);
        $tagUrl = route('tag', ['tag' => $tag]);

        $tagLink = new Link($tagUrl, $tag);
        $tagLink->data['attributes'] = [
            'class' => 'px-2 mr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
        ];

        $inlineContext->getContainer()->appendChild($tagLink);
        return true;
    }
}

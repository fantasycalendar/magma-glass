<?php

namespace App\Services\ArticleParser\InlineParser;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class ArticleTagParser implements \League\CommonMark\Parser\Inline\InlineParserInterface
{

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('(^|\s)(#+[a-zA-Z0-9\-(_)]{1,})');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        // The @ symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // This seems to be a valid match
        // Advance the cursor to the end of the match
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        // Grab the Twitter handle
        [$tag] = $inlineContext->getSubMatches();
        $profileUrl = route('tags', ['tag' => $tag]);
        $inlineContext->getContainer()->appendChild(new Link($profileUrl, '@' . $tag));
        return true;
    }
}

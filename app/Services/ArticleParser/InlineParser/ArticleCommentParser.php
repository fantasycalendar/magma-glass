<?php

namespace App\Services\ArticleParser\InlineParser;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class ArticleCommentParser implements \League\CommonMark\Parser\Inline\InlineParserInterface
{

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('%%(.*)%%');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        // The # symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if (!in_array($previousChar, [null, ' ', "\n"])) {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // This seems to be a valid match
        // Advance the cursor to the end of the match
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        if($cursor->isAtEnd()) {
            return true;
        }

        $nextChar = $cursor->peek();
        if (!in_array($nextChar, [null, ' ', "\n"])) {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        return true;
    }
}

<?php

namespace App\Services\ArticleParser\InlineParser;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\AbstractStringContainer;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class InternalImageLinkParser implements DelimiterProcessorInterface
{

//    public function getMatchDefinition(): InlineParserMatch
//    {
//        return InlineParserMatch::regex('(\!\[\[)(.*)(.png|.jpg|.jpeg|.gif|.bmp|.tif|.tiff)(\]\])');
//    }
//
//    public function parse(InlineParserContext $inlineContext): bool
//    {
//        $cursor = $inlineContext->getCursor();
//
//        dd($inlineContext->getSubMatches());
//
//        $previousChar = $cursor->peek(-1);
//        if ($previousChar !== null && $previousChar !== ' ') {
//            // peek() doesn't modify the cursor, so no need to restore state first
//            return false;
//        }
//
//        $cursor->advanceBy($inlineContext->getFullMatchLength());
//
//        [$link] = $inlineContext->getSubMatches();
//        $profileUrl = route('tags', ['tag' => $link]);
//        $inlineContext->getContainer()->appendChild(new Link($profileUrl, '@' . $link));
//        return true;
//
//    }

    public function getOpeningCharacter(): string
    {
        return '![[';
    }

    public function getClosingCharacter(): string
    {
        return ']]';
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        dd($opener);
        $output = [];
        $tmp = $opener->next();
        while($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $output[] = $tmp;
            $tmp = $next;
        }

        dd($output);

        return 0;
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
        $output = [];
        $tmp = $opener->next();
        while($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $output[] = $tmp;
            $tmp = $next;
        }

        dd($output);

//        $image = new Image();
    }
}

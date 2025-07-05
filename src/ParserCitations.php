<?php

namespace WikiConnect\ParseWiki;

use WikiConnect\ParseWiki\DataModel\Tag;
use WikiConnect\ParseWiki\ParserTags;

/**
 * Class ParserCitations
 *
 * Parses text to extract citations from wikitext.
 *
 * @package WikiConnect\ParseWiki
 */
class ParserCitations
{
    /**
     * @var string The text to parse for citations.
     */
    private string $text;

    /**
     * @var Tag[] Array of extracted citations.
     */
    private array $citations;

    /**
     * ParserCitations constructor.
     *
     * @param string $text The text to parse.
     */
    public function __construct(string $text)
    {
        $this->text = $text;
        $this->parse();
    }

    /**
     * Parse the text for <ref> tags using ParserTags and store them.
     *
     * @return void
     */
    public function parse(): void
    {
        $tagParser = new ParserTags($this->text, 'ref'); // use ParserTags to extract <ref> tags
        $this->citations = $tagParser->getTags();
    }

    /**
     * Get all citations found in the text.
     *
     * @return Tag[] Array of Tag objects.
     */
    public function getCitations(): array
    {
        return $this->citations;
    }
}

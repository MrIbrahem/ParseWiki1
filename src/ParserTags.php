<?php

namespace WikiConnect\ParseWiki;

use WikiConnect\ParseWiki\DataModel\Tag;

/**
 * Class ParserTags
 *
 * Parses text to extract tags from wikitext.
 *
 * @package WikiConnect\ParseWiki
 */
class ParserTags
{
    /**
     * @var string The text to parse for tags.
     */
    private string $text;
    private string $tagname;

    /**
     * @var Tag[] Array of extracted tags.
     */
    private array $tags;

    /**
     * ParserTags constructor.
     *
     * Initializes the parser with the given text and starts the parsing process.
     *
     * @param string $text The text to parse.
     * @param string $tagname The name of the tag to search for.
     */
    public function __construct(string $text, string $tagname = "")
    {
        $this->tagname = $tagname;
        $this->text = $text;
        $this->parse();
    }

    /**
     * Find and extract tags from the given string.
     *
     * Uses a regular expression to match tags wrapped in <ref> tags.
     *
     * @param string $string The string to search for tags.
     * @return array An array of matches found in the string.
     */
    private function find_sub_tags(string $string): array
    {
        $matches = [];

        // full tags <ref>...</ref>
        preg_match_all(
            '/<(?<tag>[a-zA-Z0-9]+)(\s[^>]*)?>(.*?)<\/\k<tag>>/is',
            $string,
            $standardMatches,
            PREG_SET_ORDER
        );

        foreach ($standardMatches as $match) {
            $matches[] = [
                'original'    => $match[0],
                'name'        => $match['tag'],
                'attributes'  => $match[2],
                'content'     => $match[3],
                'selfClosing' => false,
            ];
        }

        // Self-closing tags like <ref ... />
        preg_match_all(
            '/<(?<tag>[a-zA-Z0-9]+ *)(\s[^>]*)?\/>/is',
            $string,
            $selfClosingMatches,
            PREG_SET_ORDER
        );

        foreach ($selfClosingMatches as $match) {
            $matches[] = [
                'original'    => $match[0],
                'name'        => $match['tag'],
                'attributes'  => $match[2] ?? '',
                'content'     => '',
                'selfClosing' => true,
            ];
        }

        return $matches;
    }

    /**
     * Parse the text for tags and store them.
     *
     * Uses find_sub_tags to identify tags and initializes
     * Tag objects for each one found.
     *
     * @return void
     */
    public function parse(): void
    {
        $text_tags = $this->find_sub_tags($this->text);
        $this->tags = [];

        foreach ($text_tags as $citationData) {
            if ($this->tagname != "" && trim($citationData['name']) != trim($this->tagname)) {
                continue;
            }
            $_Citation = new Tag(
                $citationData['name'],
                $citationData['content'],
                $citationData['attributes'],
                $citationData['original'],
                $citationData['selfClosing']
            );
            $this->tags[] = $_Citation;
        }
    }

    /**
     * Get all tags found in the text.
     *
     * If a name is given, only the tags with that name are returned.
     *
     * @param string|null $name The name of the tag to return.
     *
     * @return Tag[] An array of Tag objects.
     */
    public function getTags(?string $name = null): array
    {
        if (empty($name)) {
            return $this->tags;
        }

        $outtags = [];
        foreach ($this->tags as $tag) {
            if ($tag->getName() == $name) {
                $outtags[] = $tag;
            }
        }
        return $outtags;
    }
}

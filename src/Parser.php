<?php

namespace WikiConnect\ParseWiki;

use WikiConnect\ParseWiki\ParserCategories;
use WikiConnect\ParseWiki\ParserCitations;
use WikiConnect\ParseWiki\ParserTags;
use WikiConnect\ParseWiki\ParserExternalLinks;
use WikiConnect\ParseWiki\ParserInternalLinks;
use WikiConnect\ParseWiki\ParserTemplates;

class Parser
{
    public string $text;
    public ParserCategories $categories;
    public ParserTags $tags;
    public ParserCitations $citations;
    public ParserExternalLinks $externalLinks;
    public ParserInternalLinks $internalLinks;
    public ParserTemplates $templates;

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->parse();
    }

    public function parse()
    {
        $this->categories = new ParserCategories($this->text);
        $this->citations = new ParserCitations($this->text);
        $this->tags = new ParserTags($this->text);
        $this->externalLinks = new ParserExternalLinks($this->text);
        $this->internalLinks = new ParserInternalLinks($this->text);
        $this->templates = new ParserTemplates($this->text);
    }
}

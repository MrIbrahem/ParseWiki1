<?php

namespace WikiConnect\ParseWiki\DataModel;

use WikiConnect\ParseWiki\DataModel\Attribute;

/**
 * Class Tag
 *
 * Represents a tag in a wikitext document.
 *
 * @package WikiConnect\ParseWiki\DataModel
 */
class Tag
{
    /**
     * @var string The name of the tag.
     */
    private string $tagname;
    /**
     * @var string The content of the tag.
     */
    private string $content;

    /**
     * @var string The attributes of the tag.
     */
    private string $attributes;
    /**
     * @var string The original, unprocessed text of the tag.
     */
    private string $originalText;

    private Attribute $attrs;

    private bool $selfClosing = false;
    /**
     * Tag constructor.
     *
     * @param string $tagname The name of the tag.
     * @param string $content The content of the tag.
     * @param string $attributes The attributes of the tag.
     * @param string $originalText The original, unprocessed text of the tag.
     * @param bool $selfClosing Whether the tag is self-closing.
     */
    public function __construct(string $tagname, string $content, string $attributes = "", string $originalText = "", bool $selfClosing = false)
    {
        $this->tagname = $tagname;
        $this->content = $content;
        $this->attributes = $attributes;
        $this->originalText = $originalText;
        $this->selfClosing = $selfClosing;
        $this->attrs = new Attribute($this->attributes);
    }

    public function getName(): string
    {
        return $this->tagname;
    }
    /**
     * Get the original, unprocessed text of the tag.
     * Example: <ref name="name">{{cite web|...}}</ref>
     * @return string The original text of the tag.
     */
    public function getOriginalText(): string
    {
        return $this->originalText;
    }
    /**
     * Get the content of the tag.
     * Example: {{cite web|...}}
     * @return string The content of the tag.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the attributes of the tag.
     *
     * @return string The attributes of the tag.
     */
    public function getAttributes(): string
    {
        return $this->attributes;
    }

    /**
     * Set the content of the tag.
     *
     * @param string $content The content of the tag.
     *
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    /**
     * Set the attributes of the tag.
     *
     * @param string $attributes The attributes of the tag.
     *
     * @return void
     */
    public function setAttributes(string $attributes): void
    {
        $this->attributes = $attributes;
        $this->attrs = new Attribute($this->attributes);
    }

    public function Attrs(): Attribute
    {
        return $this->attrs;
    }

    /**
     * Convert the tag to a string using the Attribute object for attribute formatting.
     *
     * @return string The tag as a string.
     */
    public function toString(): string
    {
        $attrs = $this->attrs->toString();
        if ($this->selfClosing && $this->content === "") {
            return "<" . $this->tagname . "" . trim($attrs) . "/>";
        }
        $space = (trim($attrs) != "") ? " " : "";
        return "<" . $this->tagname . $space . trim($attrs) . ">" . $this->content . "</" . trim($this->tagname) . ">";
    }
}

<?php

use PHPUnit\Framework\TestCase;
use WikiConnect\ParseWiki\ParserTags;
use WikiConnect\ParseWiki\DataModel\Tag;
use WikiConnect\ParseWiki\DataModel\Attribute;

class ParserTagsTest extends TestCase
{
    public function testSingleTag()
    {
        $text = '<gallery>Images go here</gallery>';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertCount(1, $tags);
        $this->assertInstanceOf(Tag::class, $tags[0]);
        $this->assertEquals('gallery', trim($tags[0]->getName()));
        $this->assertEquals('Images go here', $tags[0]->getContent());
    }

    public function testMultipleDifferentTags()
    {
        $text = '<gallery>Images</gallery><math>E=mc^2</math><code>echo</code>';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertCount(3, $tags);
        $this->assertEquals('gallery', trim($tags[0]->getName()));
        $this->assertEquals('math', trim($tags[1]->getName()));
        $this->assertEquals('code', trim($tags[2]->getName()));
        $this->assertEquals('E=mc^2', $tags[1]->getContent());
    }

    public function testFilterByTagName()
    {
        $text = '<gallery>Images</gallery><ref>Ignore this</ref><gallery>More images</gallery>';
        $parser = new ParserTags($text, 'gallery');
        $tags = $parser->getTags();

        $this->assertCount(2, $tags);
        foreach ($tags as $tag) {
            $this->assertEquals('gallery', trim($tag->getName()));
        }
    }

    public function testSelfClosingSourceTag()
    {
        $text = '<source lang="bash" mode="console" />';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertCount(1, $tags);
        $tag = $tags[0];

        $this->assertEquals('source', trim($tag->getName()));
        $this->assertEquals('', $tag->getContent());
        $this->assertTrue($tag->Attrs()->has('lang'));
        $this->assertEquals('"bash"', $tag->Attrs()->get('lang'));
    }

    public function testTagWithUnquotedAttributes()
    {
        $text = '<timeline type=bar height=100>Graph</timeline>';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertCount(1, $tags);
        $attrs = $tags[0]->Attrs();
        $this->assertEquals('bar', $attrs->get('type'));
        $this->assertEquals('100', $attrs->get('height'));
    }

    public function testMinimalRefTag()
    {
        $text = 'Example<ref>Ref content</ref>';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertCount(1, $tags);
        $this->assertEquals('ref', trim($tags[0]->getName()));
        $this->assertEquals('Ref content', $tags[0]->getContent());
    }

    public function testSelfClosingRefWithAttributes()
    {
        $text = '<ref name="x" group="g" />';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertCount(1, $tags);
        $tag = $tags[0];

        $this->assertEquals('ref', trim($tag->getName()));
        $this->assertEquals('', $tag->getContent());
        $this->assertEquals('"x"', $tag->Attrs()->get('name'));
        $this->assertEquals('"g"', $tag->Attrs()->get('group'));
    }

    public function testGetOriginalText()
    {
        $text = '<math>E=mc^2</math>';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertEquals('<math>E=mc^2</math>', $tags[0]->getOriginalText());
    }

    public function testToStringMatchesOriginal()
    {
        $text = '<ref name="r1">Some ref</ref>';
        $parser = new ParserTags($text);
        $tags = $parser->getTags();

        $this->assertEquals($text, $tags[0]->toString());

        $attrs = $tags[0]->Attrs();
        $attrs->set('novalue2', 'new');
        $this->assertEquals('new', $attrs->get('novalue2'));
    }

    public function testNoTags()
    {
        $text = 'Plain text without any tags.';
        $parser = new ParserTags($text);
        $this->assertCount(0, $parser->getTags());
    }
}

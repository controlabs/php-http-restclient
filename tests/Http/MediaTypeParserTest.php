<?php

namespace Controlabs\Http\Tests;

use Controlabs\Http\MediaTypeParser;
use PHPUnit\Framework\TestCase;

class MediaTypeParserTest extends TestCase
{
    public function testOffsetOnlyAcceptClosures()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid media type parser to test/test');

        $parser = new MediaTypeParser();

        $parser['test/test'] = 'jasdkl';
    }

    public function testJsonParser()
    {
        $parser = new MediaTypeParser();

        $parser->loadDefaults();

        $actual = $parser->parse('application/json', '{"name": "Controlabs Client"}');

        $expected = ['name' => 'Controlabs Client'];

        $this->assertSame($expected, $actual);
    }

    public function testXmlParser()
    {
        $parser = new MediaTypeParser();

        $parser->loadDefaults();

        $actual = $parser->parse('application/xml', '<service><test attr-test="content"></test></service>');

        $this->assertInstanceOf(\SimpleXMLElement::class, $actual);

        $actual = $parser->parse('text/xml', '<service><test attr-test="content"></test></service>');

        $this->assertInstanceOf(\SimpleXMLElement::class, $actual);
    }

    public function testHtmlParser()
    {
        $parser = new MediaTypeParser();

        $parser->loadDefaults();

        $actual = $parser->parse('text/html', '<html><body>JS is life.</body></html>');

        $this->assertInstanceOf(\DOMDocument::class, $actual);
    }

    public function testTextParser()
    {
        $parser = new MediaTypeParser();

        $parser->loadDefaults();

        $actual = $parser->parse('text/plain', 'Teste');

        $this->assertSame('Teste', $actual);
    }
}

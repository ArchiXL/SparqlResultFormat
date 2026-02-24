<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../SparqlResultFormat_body.php';

/**
 * @covers ExtSparqlResultFormat
 */
class ExtSparqlResultFormatTest extends TestCase {

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsSingleValue(): void {
		$options = [ 'key=value' ];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertArrayHasKey( 'key', $result );
		$this->assertSame( 'value', $result['key'] );
	}

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsMultipleValuesSameKeyBecomesArray(): void {
		$options = [ 'key=value1', 'key=value2', 'key=value3' ];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertArrayHasKey( 'key', $result );
		$this->assertIsArray( $result['key'] );
		$this->assertCount( 3, $result['key'] );
		$this->assertSame( [ 'value1', 'value2', 'value3' ], $result['key'] );
	}

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsKeyWithoutValueBecomesTrue(): void {
		$options = [ 'flagOption' ];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertArrayHasKey( 'flagOption', $result );
		$this->assertTrue( $result['flagOption'] );
	}

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsEmptyArray(): void {
		$options = [];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsTrimsWhitespace(): void {
		$options = [ '  key  =  value  ' ];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertArrayHasKey( 'key', $result );
		$this->assertSame( 'value', $result['key'] );
	}

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsMixedTypes(): void {
		$options = [ 'name=John', 'age=30', 'active', 'tags=php', 'tags=testing' ];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertSame( 'John', $result['name'] );
		$this->assertSame( '30', $result['age'] );
		$this->assertTrue( $result['active'] );
		$this->assertIsArray( $result['tags'] );
		$this->assertSame( [ 'php', 'testing' ], $result['tags'] );
	}

	/**
	 * @covers ExtSparqlResultFormat::extractOptions
	 */
	public function testExtractOptionsValueWithEqualsSign(): void {
		$options = [ 'query=SELECT * WHERE { ?s = ?o }' ];
		$result = ExtSparqlResultFormat::extractOptions( $options );

		$this->assertArrayHasKey( 'query', $result );
		$this->assertSame( 'SELECT * WHERE { ?s = ?o }', $result['query'] );
	}

	/**
	 * @covers ExtSparqlResultFormat::createJavascriptPrefixesArray
	 */
	public function testCreateJavascriptPrefixesArrayWithNullPrefixes(): void {
		$result = ExtSparqlResultFormat::createJavascriptPrefixesArray( null );

		$this->assertSame( 'var prefixes = [];', $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::createJavascriptPrefixesArray
	 */
	public function testCreateJavascriptPrefixesArrayWithEmptyArray(): void {
		$result = ExtSparqlResultFormat::createJavascriptPrefixesArray( [] );

		$this->assertSame( 'var prefixes = [];', $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::createJavascriptPrefixesArray
	 */
	public function testCreateJavascriptPrefixesArrayWithPrefixes(): void {
		$prefixes = [
			'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
			'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#'
		];
		$result = ExtSparqlResultFormat::createJavascriptPrefixesArray( $prefixes );

		$this->assertStringContainsString( 'var prefixes = [];', $result );
		$this->assertStringContainsString(
			"prefixes.push({pre:'rdf',ns:'http://www.w3.org/1999/02/22-rdf-syntax-ns#'});",
			$result
		);
		$this->assertStringContainsString(
			"prefixes.push({pre:'rdfs',ns:'http://www.w3.org/2000/01/rdf-schema#'});",
			$result
		);
	}

	/**
	 * @covers ExtSparqlResultFormat::createJavascriptPrefixesArray
	 */
	public function testCreateJavascriptPrefixesArrayWithSinglePrefix(): void {
		$prefixes = [ 'foaf' => 'http://xmlns.com/foaf/0.1/' ];
		$result = ExtSparqlResultFormat::createJavascriptPrefixesArray( $prefixes );

		$expected = "var prefixes = []; prefixes.push({pre:'foaf',ns:'http://xmlns.com/foaf/0.1/'});";
		$this->assertSame( $expected, $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::composeOutputScript
	 */
	public function testComposeOutputScriptCombinesJsAndHtml(): void {
		$javascript = 'console.log("test");';
		$html = '<div id="container"></div>';

		$result = ExtSparqlResultFormat::composeOutputScript( $javascript, $html );

		$this->assertStringContainsString( '<script type="text/javascript">', $result );
		$this->assertStringContainsString( 'console.log("test");', $result );
		$this->assertStringContainsString( '</script>', $result );
		$this->assertStringContainsString( '<div id="container"></div>', $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::composeOutputScript
	 */
	public function testComposeOutputScriptWithEmptyJavascript(): void {
		$javascript = '';
		$html = '<div></div>';

		$result = ExtSparqlResultFormat::composeOutputScript( $javascript, $html );

		$this->assertStringContainsString( '<script type="text/javascript">', $result );
		$this->assertStringContainsString( '</script>', $result );
		$this->assertStringContainsString( '<div></div>', $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::composeOutputScript
	 */
	public function testComposeOutputScriptWithEmptyHtml(): void {
		$javascript = 'var x = 1;';
		$html = '';

		$result = ExtSparqlResultFormat::composeOutputScript( $javascript, $html );

		$this->assertStringContainsString( '<script type="text/javascript">', $result );
		$this->assertStringContainsString( 'var x = 1;', $result );
		$this->assertStringContainsString( '</script>', $result );
	}

	/**
	 * @covers ExtSparqlResultFormat::outputHtml
	 */
	public function testOutputHtmlReplacesMarkersWithStoredElements(): void {
		ExtSparqlResultFormat::$elements = [
			'<div>First element</div>',
			'<div>Second element</div>'
		];

		$out = null;
		$text = 'Before START_SPARQL_II-0END_SPARQL_II Middle START_SPARQL_II-1END_SPARQL_II After';

		$result = ExtSparqlResultFormat::outputHtml( $out, $text );

		$this->assertTrue( $result );
		$this->assertSame(
			'Before <div>First element</div> Middle <div>Second element</div> After',
			$text
		);
	}

	/**
	 * @covers ExtSparqlResultFormat::outputHtml
	 */
	public function testOutputHtmlWithNoMarkers(): void {
		ExtSparqlResultFormat::$elements = [];

		$out = null;
		$text = 'Plain text without any markers';
		$originalText = $text;

		$result = ExtSparqlResultFormat::outputHtml( $out, $text );

		$this->assertTrue( $result );
		$this->assertSame( $originalText, $text );
	}

	/**
	 * @covers ExtSparqlResultFormat::outputHtml
	 */
	public function testOutputHtmlWithSingleMarker(): void {
		ExtSparqlResultFormat::$elements = [ '<span>Replaced content</span>' ];

		$out = null;
		$text = 'Text with START_SPARQL_II-0END_SPARQL_II marker';

		$result = ExtSparqlResultFormat::outputHtml( $out, $text );

		$this->assertTrue( $result );
		$this->assertSame( 'Text with <span>Replaced content</span> marker', $text );
	}

	/**
	 * @covers ExtSparqlResultFormat::outputHtml
	 */
	public function testOutputHtmlPreservesUnmatchedText(): void {
		ExtSparqlResultFormat::$elements = [ '<p>Element</p>' ];

		$out = null;
		$text = 'START_SPARQL_II-0END_SPARQL_II and some other content here';

		$result = ExtSparqlResultFormat::outputHtml( $out, $text );

		$this->assertTrue( $result );
		$this->assertSame( '<p>Element</p> and some other content here', $text );
	}

	protected function tearDown(): void {
		ExtSparqlResultFormat::$elements = [];
		parent::tearDown();
	}
}

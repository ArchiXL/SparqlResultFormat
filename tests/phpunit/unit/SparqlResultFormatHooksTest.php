<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../SparqlResultFormat.hooks.php';
require_once __DIR__ . '/../../../SparqlResultFormat_body.php';

/**
 * @covers SparqlResultFormatHooks
 */
class SparqlResultFormatHooksTest extends TestCase {

	/**
	 * @covers SparqlResultFormatHooks::onParserFirstCallInit
	 */
	public function testOnParserFirstCallInitRegistersAllHooks(): void {
		$parser = new Parser();

		$result = SparqlResultFormatHooks::onParserFirstCallInit( $parser );

		$this->assertTrue( $result );
		$this->assertArrayHasKey( 'sparql2table', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2graph', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2treemap', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2donutchart', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2barchart', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2piechart', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2bubblechart', $parser->registeredHooks );
		$this->assertArrayHasKey( 'sparql2csv', $parser->registeredHooks );
		$this->assertArrayHasKey( 'page2uri', $parser->registeredHooks );
		$this->assertArrayHasKey( 'smwSparqlDefaultGraph', $parser->registeredHooks );
	}

	/**
	 * @covers SparqlResultFormatHooks::onParserFirstCallInit
	 */
	public function testOnParserFirstCallInitRegistersTenHooks(): void {
		$parser = new Parser();

		SparqlResultFormatHooks::onParserFirstCallInit( $parser );

		$this->assertCount( 10, $parser->registeredHooks );
	}

	/**
	 * @covers SparqlResultFormatHooks::outputHtml
	 */
	public function testOutputHtmlDelegatesToExtSparqlResultFormat(): void {
		ExtSparqlResultFormat::$elements = [ '<div>Test</div>' ];
		$out = null;
		$text = 'Before START_SPARQL_II-0END_SPARQL_II After';

		$result = SparqlResultFormatHooks::outputHtml( $out, $text );

		$this->assertTrue( $result );
		$this->assertSame( 'Before <div>Test</div> After', $text );
	}

	/**
	 * @covers SparqlResultFormatHooks::addHTMLHeader
	 */
	public function testAddHTMLHeaderAddsModule(): void {
		$out = new StubOutputPage();

		$result = SparqlResultFormatHooks::addHTMLHeader( $out );

		$this->assertTrue( $result );
	}

	protected function tearDown(): void {
		ExtSparqlResultFormat::$elements = [];
		parent::tearDown();
	}
}

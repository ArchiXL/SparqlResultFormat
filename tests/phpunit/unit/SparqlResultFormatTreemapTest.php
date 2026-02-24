<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/SparqlResultFormat.base.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.interface.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.treemap.php';

/**
 * @covers SparqlResultFormatTreemap
 */
class SparqlResultFormatTreemapTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['wgSparqlEndpointDefinition'] = [
			'testEndpoint' => [
				'url' => 'http://example.org/sparql',
				'basicAuth' => [ 'user' => 'testuser', 'password' => 'testpass' ]
			]
		];
		$GLOBALS['wgScriptPath'] = '/w';
	}

	protected function tearDown(): void {
		unset( $GLOBALS['wgSparqlEndpointDefinition'] );
		unset( $GLOBALS['wgScriptPath'] );
		parent::tearDown();
	}

	private function createTestableInstance(): SparqlResultFormatTreemap {
		return new class extends SparqlResultFormatTreemap {
			public function __construct() {
				$this->name = 'Treemap';
				$this->description = 'Treemap visualization';
				$this->params = [
					'divId' => [ 'mandatory' => true ],
					'sparqlEndpoint' => [ 'mandatory' => true ],
					'sparqlEscapedQuery' => [ 'mandatory' => true ],
					'divStyle' => [ 'mandatory' => false ],
					'divCssClass' => [ 'mandatory' => false ],
					'spinnerImagePath' => [ 'mandatory' => false ],
					'rootElement' => [ 'mandatory' => true ],
					'leavesLinkPattern' => [ 'mandatory' => false ],
					'openLinkOnLeaves' => [ 'mandatory' => false ],
				];
			}
		};
	}

	/**
	 * @covers SparqlResultFormatTreemap::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeContainsDivId(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'myTreemap',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE {}',
			'rootElement' => 'http://example.org/root'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='myTreemap-container'", $html );
		$this->assertStringContainsString( "id='myTreemap'", $html );
	}

	/**
	 * @covers SparqlResultFormatTreemap::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeContainsTreemapClass(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'treemapDiv',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE {}',
			'rootElement' => 'http://example.org/root'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( 'd3-treemap', $html );
		$this->assertStringContainsString( 'sparqlresultformat-treemap', $html );
	}

	/**
	 * @covers SparqlResultFormatTreemap::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeContainsTooltipDiv(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'tooltipTreemap',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE {}',
			'rootElement' => 'http://example.org/root'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( 'tooltip-treemap', $html );
	}

	/**
	 * @covers SparqlResultFormatTreemap::generateLaunchScript
	 */
	public function testGenerateLaunchScriptContainsTreemapModule(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'launchTreemap',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE {}',
			'rootElement' => 'http://example.org/root'
		];

		$script = $instance->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.treemap'", $script );
		$this->assertStringContainsString( "spqlib.sparql2Treemap(config)", $script );
	}
}

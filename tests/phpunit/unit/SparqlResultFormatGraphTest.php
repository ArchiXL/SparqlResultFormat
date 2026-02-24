<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/SparqlResultFormat.base.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.interface.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.graph.php';

/**
 * @covers SparqlResultFormatGraph
 */
class SparqlResultFormatGraphTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['wgSparqlEndpointDefinition'] = [
			'testEndpoint' => [
				'url' => 'https://example.org/sparql',
				'basicAuth' => [ 'user' => 'testuser', 'password' => 'testpass' ]
			]
		];
		$GLOBALS['wgScriptPath'] = '/w';
		$GLOBALS['wgServer'] = 'https://wiki.example.org';
		$GLOBALS['wgSrfMaxNumNodes'] = 500;
		$GLOBALS['wgSrfQueryTimeout'] = 30000;
	}

	protected function tearDown(): void {
		unset( $GLOBALS['wgSparqlEndpointDefinition'] );
		unset( $GLOBALS['wgScriptPath'] );
		unset( $GLOBALS['wgServer'] );
		unset( $GLOBALS['wgSrfMaxNumNodes'] );
		unset( $GLOBALS['wgSrfQueryTimeout'] );
		parent::tearDown();
	}

	private function createTestableInstance(): SparqlResultFormatGraph {
		return new class extends SparqlResultFormatGraph {
			public function __construct() {
				$this->name = 'Graph';
				$this->description = 'Graph visualization';
				$this->params = [
					'divId' => [ 'mandatory' => true ],
					'sparqlEndpoint' => [ 'mandatory' => true ],
					'sparqlEscapedQuery' => [ 'mandatory' => true ],
					'divStyle' => [ 'mandatory' => false ],
					'divCssClass' => [ 'mandatory' => false ],
					'spinnerImagePath' => [ 'mandatory' => false ],
					'rootElement' => [ 'mandatory' => false ],
					'nodeConfiguration' => [ 'mandatory' => false ],
					'edgeConfiguration' => [ 'mandatory' => false ],
					'layout' => [ 'mandatory' => false ],
					'layoutOptions' => [ 'mandatory' => false ],
					'showZoomControls' => [ 'mandatory' => false ],
					'showLegend' => [ 'mandatory' => false ],
					'showDownloadImageLink' => [ 'mandatory' => false ],
					'minZoom' => [ 'mandatory' => false ],
					'maxZoom' => [ 'mandatory' => false ],
					'action.zoom.controls.visible' => [ 'mandatory' => false ],
				];
			}
		};
	}

	/**
	 * @covers SparqlResultFormatGraph::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeContainsCytoscapeClass(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'testGraph',
			'divStyle' => 'width:100%;height:500px;'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( 'cytoscape-graph', $html );
		$this->assertStringContainsString( "id='testGraph-container'", $html );
		$this->assertStringContainsString( "id='testGraph'", $html );
	}

	/**
	 * @covers SparqlResultFormatGraph::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeContainsZoomControls(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'zoomGraph',
			'showZoomControls' => 'true'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( 'ii-graph-zoom-controls', $html );
		$this->assertStringContainsString( 'fa-search-plus', $html );
		$this->assertStringContainsString( 'fa-search-minus', $html );
	}

	/**
	 * @covers SparqlResultFormatGraph::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeContainsGraphContainer(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'containerGraph'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( 'ii-graph-container', $html );
	}

	/**
	 * @covers SparqlResultFormatGraph::generateLaunchScript
	 */
	public function testGenerateLaunchScriptContainsGraphModule(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'launchGraph',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE {}'
		];

		$script = $instance->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.graph'", $script );
		$this->assertStringContainsString( "spqlib.sparql2Graph(config)", $script );
	}

	/**
	 * @covers SparqlResultFormatGraph::generateLaunchScript
	 */
	public function testGenerateLaunchScriptLoadsMainModule(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'mainModuleGraph',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE {}'
		];

		$script = $instance->generateLaunchScript( $options );

		$this->assertStringContainsString( 'ext.SparqlResultFormat.main', $script );
	}
}

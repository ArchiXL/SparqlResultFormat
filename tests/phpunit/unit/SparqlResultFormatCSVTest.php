<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/SparqlResultFormat.base.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.interface.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.csv.php';

/**
 * @covers SparqlResultFormatCSV
 */
class SparqlResultFormatCSVTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['wgSparqlEndpointDefinition'] = [
			'testEndpoint' => [
				'url' => 'https://example.org/sparql',
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

	private function createTestableInstance(): SparqlResultFormatCSV {
		return new class extends SparqlResultFormatCSV {
			public function __construct() {
				$this->name = 'CSV Export';
				$this->description = 'Export as CSV';
				$this->params = [
					'divId' => [ 'mandatory' => true, 'description' => 'Div ID' ],
					'sparqlEndpoint' => [ 'mandatory' => true, 'description' => 'Endpoint' ],
					'sparqlEscapedQuery' => [ 'mandatory' => true, 'description' => 'Query' ],
					'divStyle' => [ 'mandatory' => false, 'description' => 'Style' ],
					'headerMapping' => [ 'mandatory' => false, 'description' => 'Header mapping' ],
					'filename' => [ 'mandatory' => false, 'description' => 'Filename', 'default' => self::DEFAULT_FILENAME ],
					'separator' => [ 'mandatory' => false, 'description' => 'Separator', 'default' => self::DEFAULT_SEPARATOR ],
					'linkButtonLabel' => [ 'mandatory' => false, 'description' => 'Button label' ],
					'linkButtonCSSClass' => [ 'mandatory' => false, 'description' => 'Button class' ],
					'label' => [ 'mandatory' => false, 'description' => 'Label', 'default' => self::DEFAULT_LABEL ]
				];
			}
		};
	}

	/**
	 * @covers SparqlResultFormatCSV::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeWithDivId(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'myTestDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='myTestDiv'", $html );
		$this->assertStringContainsString( "id='myTestDiv-wrapper'", $html );
		$this->assertStringContainsString( "id='myTestDiv-click'", $html );
	}

	/**
	 * @covers SparqlResultFormatCSV::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeWithButton(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'csvDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }',
			'linkButtonLabel' => 'Export CSV',
			'linkButtonCSSClass' => 'btn btn-primary'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( '<button', $html );
		$this->assertStringContainsString( 'Export CSV', $html );
		$this->assertStringContainsString( "class='btn btn-primary'", $html );
	}

	/**
	 * @covers SparqlResultFormatCSV::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeWithDefaultLabel(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'defaultLabelDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }'
		];

		$html = $instance->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( '<a', $html );
		$this->assertStringContainsString( 'Download as CSV', $html );
	}

	/**
	 * @covers SparqlResultFormatCSV::generateConfig
	 */
	public function testGenerateConfigWithFilename(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'configDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }',
			'filename' => 'custom_export.csv'
		];

		$config = $instance->generateConfig( $options );

		$this->assertStringContainsString( "config.filename = 'custom_export.csv'", $config );
	}

	/**
	 * @covers SparqlResultFormatCSV::generateConfig
	 */
	public function testGenerateConfigWithDefaultFilename(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'defaultFilenameDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }'
		];

		$config = $instance->generateConfig( $options );

		$this->assertStringContainsString( "config.filename = 'export.csv'", $config );
	}

	/**
	 * @covers SparqlResultFormatCSV::generateConfig
	 */
	public function testGenerateConfigWithSeparator(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'separatorDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }',
			'separator' => ','
		];

		$config = $instance->generateConfig( $options );

		$this->assertStringContainsString( "config.separator = ','", $config );
	}

	/**
	 * @covers SparqlResultFormatCSV::generateLaunchScript
	 */
	public function testGenerateLaunchScriptContainsClickHandler(): void {
		$instance = $this->createTestableInstance();
		$options = [
			'divId' => 'launchDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }'
		];

		$script = $instance->generateLaunchScript( $options );

		$this->assertStringContainsString( "$('#launchDiv-click').click", $script );
		$this->assertStringContainsString( 'mw.loader.using', $script );
		$this->assertStringContainsString( 'spqlib.sparql2CSV(config)', $script );
	}

	/**
	 * @covers SparqlResultFormatCSV
	 */
	public function testDefaultConstants(): void {
		$this->assertSame( 'export.csv', SparqlResultFormatCSV::DEFAULT_FILENAME );
		$this->assertSame( ';', SparqlResultFormatCSV::DEFAULT_SEPARATOR );
		$this->assertSame( 'Download as CSV', SparqlResultFormatCSV::DEFAULT_LABEL );
	}
}

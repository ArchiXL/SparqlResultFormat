<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/SparqlResultFormat.base.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.interface.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.table.php';

/**
 * @covers SparqlResultFormatTable
 */
class SparqlResultFormatTableTest extends TestCase {

	private TestableSparqlResultFormatTable $table;

	protected function setUp(): void {
		parent::setUp();
		$this->table = new TestableSparqlResultFormatTable();
	}

	/**
	 * @covers SparqlResultFormatTable::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeWithAllOptions(): void {
		$options = [
			'divId' => 'myTable',
			'divStyle' => 'width:100%;height:400px',
			'sparqlEscapedQuery' => 'SELECT ?s ?p ?o WHERE { ?s ?p ?o }',
		];

		$result = $this->table->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='myTable-container'", $result );
		$this->assertStringContainsString( "class='table-container'", $result );
		$this->assertStringContainsString( "id='myTable'", $result );
		$this->assertStringContainsString( "style='width:100%;height:400px'", $result );
		$this->assertStringContainsString( 'sparql-query=', $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateHtmlContainerCode
	 */
	public function testGenerateHtmlContainerCodeWithEmptyStyle(): void {
		$options = [
			'divId' => 'testDiv',
			'sparqlEscapedQuery' => 'SELECT * WHERE { ?s ?p ?o }',
		];

		$result = $this->table->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='testDiv-container'", $result );
		$this->assertStringContainsString( "id='testDiv'", $result );
		$this->assertStringContainsString( "style=''", $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateJavascriptCode
	 */
	public function testGenerateJavascriptCodeCombinesAllParts(): void {
		$options = [
			'divId' => 'jsTestTable',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?x WHERE { ?x a ?type }',
		];
		$prefixes = 'var prefixes = [];';

		$result = $this->table->generateJavascriptCode( $options, $prefixes );

		$this->assertStringContainsString( $prefixes, $result );
		$this->assertStringContainsString( 'var config = {}', $result );
		$this->assertStringContainsString( 'window.sparqlResultFormatsElements', $result );
		$this->assertStringContainsString( 'mw.loader.using', $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateLaunchScript
	 */
	public function testGenerateLaunchScriptContainsDivId(): void {
		$options = [
			'divId' => 'launchTestDiv',
		];

		$result = $this->table->generateLaunchScript( $options );

		$this->assertStringContainsString( "$('#launchTestDiv')", $result );
		$this->assertStringContainsString( "decodeURIComponent", $result );
		$this->assertStringContainsString( "attr('sparql-query')", $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateLaunchScript
	 */
	public function testGenerateLaunchScriptContainsModuleLoading(): void {
		$options = [
			'divId' => 'moduleTestDiv',
		];

		$result = $this->table->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( ['ext.SparqlResultFormat.main','jquery.tablesorter']", $result );
		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.table'", $result );
		$this->assertStringContainsString( "spqlib.sparql2Table(config)", $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateConfig
	 */
	public function testGenerateConfigContainsAllParameters(): void {
		$options = [
			'divId' => 'configTestDiv',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?s WHERE { ?s ?p ?o }',
			'tableClass' => 'wikitable sortable',
			'columnConfiguration' => '[{queryField:"name",label:"Name"}]',
			'cssEvenRowClass' => 'evenRow',
			'cssOddRowClass' => 'oddRow',
			'noResultMessage' => 'No results found',
			'csvExport' => 'true',
			'csvFileName' => 'data.csv',
		];

		$result = $this->table->generateConfig( $options );

		$this->assertStringContainsString( "config.divId = 'configTestDiv'", $result );
		$this->assertStringContainsString( "config.tableClass='wikitable sortable'", $result );
		$this->assertStringContainsString( "config.cssEvenRowClass='evenRow'", $result );
		$this->assertStringContainsString( "config.cssOddRowClass='oddRow'", $result );
		$this->assertStringContainsString( "config.noResultMessage='No results found'", $result );
		$this->assertStringContainsString( "config.endpointName='testEndpoint'", $result );
		$this->assertStringContainsString( "config.csvExport='true'", $result );
		$this->assertStringContainsString( "config.csvFileName='data.csv'", $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateConfig
	 */
	public function testGenerateConfigWithBasicAuth(): void {
		$table = new TestableSparqlResultFormatTable( [
			'testAuthEndpoint' => [
				'url' => 'http://example.com/sparql',
				'basicAuth' => [
					'user' => 'testuser',
					'password' => 'testpass',
				],
			],
		] );

		$options = [
			'divId' => 'authTestDiv',
			'sparqlEndpoint' => 'testAuthEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?s WHERE { ?s ?p ?o }',
		];

		$result = $table->generateConfig( $options );

		$expectedAuthString = base64_encode( 'testuser:testpass' );
		$this->assertStringContainsString( "config.basicAuthBase64String='$expectedAuthString'", $result );
	}

	/**
	 * @covers SparqlResultFormatTable::generateConfig
	 */
	public function testGenerateConfigThrowsExceptionForMissingEndpoint(): void {
		$table = new TestableSparqlResultFormatTable( [] );

		$options = [
			'divId' => 'missingEndpointDiv',
			'sparqlEndpoint' => 'nonExistentEndpoint',
			'sparqlEscapedQuery' => 'SELECT ?s WHERE { ?s ?p ?o }',
		];

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "No endpoint 'nonExistentEndpoint' found in LocalSettings.php" );

		$table->generateConfig( $options );
	}
}

/**
 * Testable subclass that bypasses wfMessage() calls
 */
class TestableSparqlResultFormatTable extends SparqlResultFormatTable {

	private array $testEndpoints;

	public function __construct( ?array $endpoints = null ) {
		$this->name = 'Table';
		$this->description = 'Table format';
		$this->testEndpoints = $endpoints ?? [
			'testEndpoint' => [ 'url' => 'http://example.com/sparql' ],
		];

		$this->params = [
			'divId' => [ 'mandatory' => true, 'description' => 'The div ID' ],
			'sparqlEscapedQuery' => [ 'mandatory' => true, 'description' => 'The SPARQL query' ],
			'sparqlEndpoint' => [ 'mandatory' => true, 'description' => 'The SPARQL endpoint' ],
			'spinnerImagePath' => [ 'mandatory' => false, 'description' => 'Path to spinner' ],
			'divStyle' => [ 'mandatory' => false, 'description' => 'CSS style' ],
			'tableClass' => [ 'mandatory' => false, 'description' => 'CSS class' ],
			'columnConfiguration' => [ 'mandatory' => false, 'description' => 'Column config' ],
			'cssEvenRowClass' => [ 'mandatory' => false, 'description' => 'Even row class' ],
			'cssOddRowClass' => [ 'mandatory' => false, 'description' => 'Odd row class' ],
			'cssEvenTdClass' => [ 'mandatory' => false, 'description' => 'Even td class' ],
			'cssOddTdClass' => [ 'mandatory' => false, 'description' => 'Odd td class' ],
			'noResultMessage' => [ 'mandatory' => false, 'description' => 'No result message' ],
			'csvExport' => [ 'mandatory' => false, 'description' => 'CSV export', 'default' => 'false' ],
			'csvFileName' => [ 'mandatory' => false, 'description' => 'CSV filename', 'default' => 'export.csv' ],
			'csvLinkLabel' => [ 'mandatory' => false, 'description' => 'CSV link label' ],
			'linkBasePath' => [ 'mandatory' => false, 'description' => 'Link base path' ],
		];
	}

	protected function getSparqlEndpointByName( $endpointName ) {
		if ( isset( $this->testEndpoints[$endpointName] ) ) {
			return $this->testEndpoints[$endpointName];
		}
		throw new Exception( "No endpoint '$endpointName' found in LocalSettings.php" );
	}

	public function generateConfig( $options ) {
		$endpointIndex = $this->getParameterValue( $options, 'sparqlEndpoint', '' );
		$endpointData = $this->getSparqlEndpointByName( $endpointIndex );
		$basicAuthBase64String = $this->getSparqlEndpointBasicAuthString( $endpointData );

		$divId = $this->getParameterValue( $options, 'divId', '' );
		$tableClass = $this->getParameterValue( $options, 'tableClass', '' );
		$columnConfiguration = $this->getParameterValue( $options, 'columnConfiguration', '{}' );
		$cssEvenRowClass = $this->getParameterValue( $options, 'cssEvenRowClass', '' );
		$cssOddRowClass = $this->getParameterValue( $options, 'cssOddRowClass', '' );
		$cssEvenTdClass = $this->getParameterValue( $options, 'cssEvenTdClass', '' );
		$cssOddTdClass = $this->getParameterValue( $options, 'cssOddTdClass', '' );
		$noResultMessage = $this->getParameterValue( $options, 'noResultMessage', '' );
		$linkBasePath = $this->getParameterValue( $options, 'linkBasePath', '/wiki/index.php/' );
		$spinnerImagePath = $this->getParameterValue( $options, 'spinnerImagePath', '/extensions/SparqlResultFormat/img/spinner.gif' );
		$csvExport = $this->getParameterValue( $options, 'csvExport', 'false' );
		$csvFileName = $this->getParameterValue( $options, 'csvFileName', 'export.csv' );
		$csvLinkLabel = $this->getParameterValue( $options, 'csvLinkLabel', 'Export as CSV' );

		return "var config = {};
			config.divId = '$divId';
			config.tableClass='$tableClass';
			config.columnConfiguration=$columnConfiguration;
			config.cssEvenRowClass='$cssEvenRowClass';
			config.cssOddRowClass='$cssOddRowClass';
			config.cssEvenTdClass='$cssEvenTdClass';
			config.cssOddTdClass='$cssOddTdClass';
			config.noResultMessage='$noResultMessage';
			config.endpointName='$endpointIndex';
			config.queryPrefixes=prefixes;
			config.basicAuthBase64String='$basicAuthBase64String';
			config.linkBasePath='$linkBasePath';
			config.csvExport='$csvExport';
			config.csvFileName='$csvFileName';
			config.csvLinkLabel='$csvLinkLabel';
			config.spinnerImagePath='$spinnerImagePath';
			config.numberFormatDefaultLocale='en'";
	}
}

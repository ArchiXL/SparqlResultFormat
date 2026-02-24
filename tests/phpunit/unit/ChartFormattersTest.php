<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/SparqlResultFormat.base.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.interface.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.barchart.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.piechart.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.donutchart.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.bubblechart.php';

/**
 * @covers SparqlResultFormatBarChart
 * @covers SparqlResultFormatPieChart
 * @covers SparqlResultFormatDonutChart
 * @covers SparqlResultFormatBubbleChart
 */
class ChartFormattersTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['wgScriptPath'] = '/w';
		$GLOBALS['wgSparqlEndpointDefinition'] = [
			'testEndpoint' => [
				'url' => 'http://example.org/sparql',
				'basicAuth' => [ 'user' => 'testuser', 'password' => 'testpass' ]
			]
		];
	}

	protected function tearDown(): void {
		unset( $GLOBALS['wgScriptPath'] );
		unset( $GLOBALS['wgSparqlEndpointDefinition'] );
		parent::tearDown();
	}

	private function getDefaultOptions(): array {
		return [
			'divId' => 'testChart',
			'sparqlEndpoint' => 'testEndpoint',
			'sparqlEscapedQuery' => 'SELECT%20%3Fx%20WHERE%20%7B%7D',
			'divStyle' => 'width:500px;height:400px;',
			'divCssClass' => 'my-chart-class',
		];
	}

	/**
	 * @covers SparqlResultFormatBarChart::generateHtmlContainerCode
	 */
	public function testBarChartGenerateHtmlContainerCode(): void {
		$formatter = new TestableBarChart();
		$options = $this->getDefaultOptions();

		$html = $formatter->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='testChart-container'", $html );
		$this->assertStringContainsString( "class='sparqlresultformat-barchart'", $html );
	}

	/**
	 * @covers SparqlResultFormatBarChart::generateLaunchScript
	 */
	public function testBarChartGenerateLaunchScript(): void {
		$formatter = new TestableBarChart();
		$options = $this->getDefaultOptions();

		$script = $formatter->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.barchart'", $script );
		$this->assertStringContainsString( "spqlib.sparql2BarChart(config)", $script );
	}

	/**
	 * @covers SparqlResultFormatPieChart::generateHtmlContainerCode
	 */
	public function testPieChartGenerateHtmlContainerCode(): void {
		$formatter = new TestablePieChart();
		$options = $this->getDefaultOptions();

		$html = $formatter->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='testChart-container'", $html );
		$this->assertStringContainsString( "class='sparqlresultformat-piechart'", $html );
	}

	/**
	 * @covers SparqlResultFormatPieChart::generateLaunchScript
	 */
	public function testPieChartGenerateLaunchScript(): void {
		$formatter = new TestablePieChart();
		$options = $this->getDefaultOptions();

		$script = $formatter->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.piechart'", $script );
		$this->assertStringContainsString( "spqlib.sparql2PieChart(config)", $script );
	}

	/**
	 * @covers SparqlResultFormatDonutChart::generateHtmlContainerCode
	 */
	public function testDonutChartGenerateHtmlContainerCode(): void {
		$formatter = new TestableDonutChart();
		$options = $this->getDefaultOptions();

		$html = $formatter->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='testChart-container'", $html );
		$this->assertStringContainsString( "class='sparqlresultformat-donutchart'", $html );
	}

	/**
	 * @covers SparqlResultFormatDonutChart::generateLaunchScript
	 */
	public function testDonutChartGenerateLaunchScript(): void {
		$formatter = new TestableDonutChart();
		$options = $this->getDefaultOptions();

		$script = $formatter->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.donutchart'", $script );
		$this->assertStringContainsString( "spqlib.sparql2DonutChart(config)", $script );
	}

	/**
	 * @covers SparqlResultFormatBubbleChart::generateHtmlContainerCode
	 */
	public function testBubbleChartGenerateHtmlContainerCode(): void {
		$formatter = new TestableBubbleChart();
		$options = $this->getDefaultOptions();

		$html = $formatter->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( "id='testChart-container'", $html );
		$this->assertStringContainsString( "class='sparqlresultformat-bubblechart'", $html );
		$this->assertStringContainsString( "id='testChart-legend'", $html );
	}

	/**
	 * @covers SparqlResultFormatBubbleChart::generateLaunchScript
	 */
	public function testBubbleChartGenerateLaunchScript(): void {
		$formatter = new TestableBubbleChart();
		$options = $this->getDefaultOptions();

		$script = $formatter->generateLaunchScript( $options );

		$this->assertStringContainsString( "mw.loader.using( 'ext.SparqlResultFormat.bubblechart'", $script );
		$this->assertStringContainsString( "spqlib.sparql2BubbleChart(config)", $script );
	}

	/**
	 * Test all charts use different CSS classes
	 */
	public function testAllChartsUseDifferentCssClasses(): void {
		$options = $this->getDefaultOptions();

		$barHtml = ( new TestableBarChart() )->generateHtmlContainerCode( $options );
		$pieHtml = ( new TestablePieChart() )->generateHtmlContainerCode( $options );
		$donutHtml = ( new TestableDonutChart() )->generateHtmlContainerCode( $options );
		$bubbleHtml = ( new TestableBubbleChart() )->generateHtmlContainerCode( $options );

		$this->assertStringContainsString( 'sparqlresultformat-barchart', $barHtml );
		$this->assertStringContainsString( 'sparqlresultformat-piechart', $pieHtml );
		$this->assertStringContainsString( 'sparqlresultformat-donutchart', $donutHtml );
		$this->assertStringContainsString( 'sparqlresultformat-bubblechart', $bubbleHtml );
	}
}

// Testable subclasses
class TestableBarChart extends SparqlResultFormatBarChart {
	public function __construct() {
		$this->name = 'BarChart';
		$this->params = [
			'divId' => [ 'mandatory' => true ],
			'sparqlEndpoint' => [ 'mandatory' => true ],
			'sparqlEscapedQuery' => [ 'mandatory' => true ],
			'divStyle' => [ 'mandatory' => false ],
			'divCssClass' => [ 'mandatory' => false ],
			'divCssClassFullScreen' => [ 'mandatory' => false ],
			'spinnerImagePath' => [ 'mandatory' => false ],
			'seriesConfiguration' => [ 'mandatory' => false ],
			'extraOption' => [ 'mandatory' => false ],
		];
		$this->extraOpts = [ 'chart.direction' => [] ];
	}
}

class TestablePieChart extends SparqlResultFormatPieChart {
	public function __construct() {
		$this->name = 'PieChart';
		$this->params = [
			'divId' => [ 'mandatory' => true ],
			'sparqlEndpoint' => [ 'mandatory' => true ],
			'sparqlEscapedQuery' => [ 'mandatory' => true ],
			'divStyle' => [ 'mandatory' => false ],
			'divCssClass' => [ 'mandatory' => false ],
			'divCssClassFullScreen' => [ 'mandatory' => false ],
			'spinnerImagePath' => [ 'mandatory' => false ],
			'extraOption' => [ 'mandatory' => false ],
		];
		$this->extraOpts = [ 'chart.title' => [] ];
	}
}

class TestableDonutChart extends SparqlResultFormatDonutChart {
	public function __construct() {
		$this->name = 'DonutChart';
		$this->params = [
			'divId' => [ 'mandatory' => true ],
			'sparqlEndpoint' => [ 'mandatory' => true ],
			'sparqlEscapedQuery' => [ 'mandatory' => true ],
			'divStyle' => [ 'mandatory' => false ],
			'divCssClass' => [ 'mandatory' => false ],
			'divCssClassFullScreen' => [ 'mandatory' => false ],
			'spinnerImagePath' => [ 'mandatory' => false ],
			'extraOption' => [ 'mandatory' => false ],
		];
		$this->extraOpts = [ 'chart.title' => [] ];
	}
}

class TestableBubbleChart extends SparqlResultFormatBubbleChart {
	public function __construct() {
		$this->name = 'BubbleChart';
		$this->params = [
			'divId' => [ 'mandatory' => true ],
			'sparqlEndpoint' => [ 'mandatory' => true ],
			'sparqlEscapedQuery' => [ 'mandatory' => true ],
			'divStyle' => [ 'mandatory' => false ],
			'divCssClass' => [ 'mandatory' => false ],
			'divCssClassFullScreen' => [ 'mandatory' => false ],
			'spinnerImagePath' => [ 'mandatory' => false ],
			'extraOption' => [ 'mandatory' => false ],
		];
		$this->extraOpts = [ 'chart.legend.show' => [] ];
	}
}

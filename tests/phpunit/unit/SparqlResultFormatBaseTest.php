<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/SparqlResultFormat.base.php';
require_once __DIR__ . '/../../../src/SparqlResultFormat.interface.php';

/**
 * @covers SparqlResultFormatBase
 */
class SparqlResultFormatBaseTest extends TestCase {

	/**
	 * Testable subclass that exposes protected methods and allows setting params/extraOpts.
	 */
	private function getTestableInstance( array $params = [], array $extraOpts = [] ): TestableSparqlResultFormatBase {
		return new TestableSparqlResultFormatBase( $params, $extraOpts );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsValueWhenParamPresent(): void {
		$params = [
			'testParam' => [
				'type' => 'string',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'testParam' => 'myValue' ];

		$result = $instance->exposedGetParameterValue( $options, 'testParam', 'default' );

		$this->assertSame( 'myValue', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsDefaultWhenOptionalParamMissing(): void {
		$params = [
			'optionalParam' => [
				'type' => 'string',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [];

		$result = $instance->exposedGetParameterValue( $options, 'optionalParam', 'defaultValue' );

		$this->assertSame( 'defaultValue', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueThrowsExceptionForMandatoryParamMissing(): void {
		$params = [
			'mandatoryParam' => [
				'type' => 'string',
				'mandatory' => true,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [];

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Param mandatoryParam must be specified' );

		$instance->exposedGetParameterValue( $options, 'mandatoryParam', null );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueThrowsExceptionForUndefinedParam(): void {
		$params = [];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'undefinedParam' => 'value' ];

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Param undefinedParam is not defined in params definition.' );

		$instance->exposedGetParameterValue( $options, 'undefinedParam', 'default' );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsTrueForBooleanTrue(): void {
		$params = [
			'boolParam' => [
				'type' => 'boolean',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'boolParam' => 'true' ];

		$result = $instance->exposedGetParameterValue( $options, 'boolParam', false, true );

		$this->assertTrue( $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsFalseForBooleanFalse(): void {
		$params = [
			'boolParam' => [
				'type' => 'boolean',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'boolParam' => 'false' ];

		$result = $instance->exposedGetParameterValue( $options, 'boolParam', true, true );

		$this->assertFalse( $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsTrueForBooleanOne(): void {
		$params = [
			'boolParam' => [
				'type' => 'boolean',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'boolParam' => '1' ];

		$result = $instance->exposedGetParameterValue( $options, 'boolParam', false, true );

		$this->assertTrue( $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsFalseForBooleanZero(): void {
		$params = [
			'boolParam' => [
				'type' => 'boolean',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'boolParam' => '0' ];

		$result = $instance->exposedGetParameterValue( $options, 'boolParam', true, true );

		$this->assertFalse( $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueReturnsNullForInvalidBooleanValue(): void {
		$params = [
			'boolParam' => [
				'type' => 'boolean',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'boolParam' => 'invalid' ];

		$result = $instance->exposedGetParameterValue( $options, 'boolParam', false, true );

		$this->assertNull( $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueHandlesArrayValues(): void {
		$params = [
			'arrayParam' => [
				'type' => 'array',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'arrayParam' => [ 'value1', 'value2', 'value3' ] ];

		$result = $instance->exposedGetParameterValue( $options, 'arrayParam', [] );

		$this->assertSame( [ 'value1', 'value2', 'value3' ], $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueDecodesHtmlEntitiesInString(): void {
		$params = [
			'htmlParam' => [
				'type' => 'string',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'htmlParam' => 'value &amp; more &quot;text&quot;' ];

		$result = $instance->exposedGetParameterValue( $options, 'htmlParam', '' );

		$this->assertSame( 'value & more "text"', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueDecodesHtmlEntitiesInArray(): void {
		$params = [
			'arrayParam' => [
				'type' => 'array',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'arrayParam' => [ '&lt;tag&gt;', '&amp;', '&quot;quoted&quot;' ] ];

		$result = $instance->exposedGetParameterValue( $options, 'arrayParam', [] );

		$this->assertSame( [ '<tag>', '&', '"quoted"' ], $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getParameterValue
	 */
	public function testGetParameterValueTrimsParamName(): void {
		$params = [
			'trimmedParam' => [
				'type' => 'string',
				'mandatory' => false,
			],
		];
		$instance = $this->getTestableInstance( $params );
		$options = [ 'trimmedParam' => 'value' ];

		$result = $instance->exposedGetParameterValue( $options, '  trimmedParam  ', 'default' );

		$this->assertSame( 'value', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getSparqlEndpointBasicAuthString
	 */
	public function testGetSparqlEndpointBasicAuthStringReturnsEncodedCredentials(): void {
		$instance = $this->getTestableInstance();
		$endpointData = [
			'url' => 'http://example.com/sparql',
			'basicAuth' => [
				'user' => 'testuser',
				'password' => 'testpassword',
			],
		];

		$result = $instance->exposedGetSparqlEndpointBasicAuthString( $endpointData );

		$expected = base64_encode( 'testuser:testpassword' );
		$this->assertSame( $expected, $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getSparqlEndpointBasicAuthString
	 */
	public function testGetSparqlEndpointBasicAuthStringReturnsEmptyStringWhenNoBasicAuth(): void {
		$instance = $this->getTestableInstance();
		$endpointData = [
			'url' => 'http://example.com/sparql',
		];

		$result = $instance->exposedGetSparqlEndpointBasicAuthString( $endpointData );

		$this->assertSame( '', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getSparqlEndpointBasicAuthString
	 */
	public function testGetSparqlEndpointBasicAuthStringHandlesMissingUser(): void {
		$instance = $this->getTestableInstance();
		$endpointData = [
			'basicAuth' => [
				'password' => 'onlypassword',
			],
		];

		$result = $instance->exposedGetSparqlEndpointBasicAuthString( $endpointData );

		$expected = base64_encode( ':onlypassword' );
		$this->assertSame( $expected, $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getSparqlEndpointBasicAuthString
	 */
	public function testGetSparqlEndpointBasicAuthStringHandlesMissingPassword(): void {
		$instance = $this->getTestableInstance();
		$endpointData = [
			'basicAuth' => [
				'user' => 'onlyuser',
			],
		];

		$result = $instance->exposedGetSparqlEndpointBasicAuthString( $endpointData );

		$expected = base64_encode( 'onlyuser:' );
		$this->assertSame( $expected, $result );
	}

	/**
	 * @covers SparqlResultFormatBase::checkExtraOptions
	 */
	public function testCheckExtraOptionsAcceptsValidOption(): void {
		$extraOpts = [
			'validOption' => [
				'description' => 'A valid option',
			],
		];
		$instance = $this->getTestableInstance( [], $extraOpts );

		// Should not throw an exception
		$instance->exposedCheckExtraOptions( 'validOption:somevalue' );

		$this->assertTrue( true );
	}

	/**
	 * @covers SparqlResultFormatBase::checkExtraOptions
	 */
	public function testCheckExtraOptionsThrowsExceptionForInvalidOption(): void {
		$extraOpts = [
			'validOption' => [
				'description' => 'A valid option',
			],
		];
		$instance = $this->getTestableInstance( [], $extraOpts );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Extra Option invalidOption is not declared as a valid option for this format!' );

		$instance->exposedCheckExtraOptions( 'invalidOption:somevalue' );
	}

	/**
	 * @covers SparqlResultFormatBase::checkExtraOptions
	 */
	public function testCheckExtraOptionsAcceptsArrayOfValidOptions(): void {
		$extraOpts = [
			'option1' => [ 'description' => 'Option 1' ],
			'option2' => [ 'description' => 'Option 2' ],
		];
		$instance = $this->getTestableInstance( [], $extraOpts );

		// Should not throw an exception
		$instance->exposedCheckExtraOptions( [ 'option1:value1', 'option2:value2' ] );

		$this->assertTrue( true );
	}

	/**
	 * @covers SparqlResultFormatBase::checkExtraOptions
	 */
	public function testCheckExtraOptionsThrowsExceptionForArrayWithInvalidOption(): void {
		$extraOpts = [
			'validOption' => [ 'description' => 'A valid option' ],
		];
		$instance = $this->getTestableInstance( [], $extraOpts );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Extra Option invalidOption is not declared as a valid option for this format!' );

		$instance->exposedCheckExtraOptions( [ 'validOption:value', 'invalidOption:value' ] );
	}

	/**
	 * @covers SparqlResultFormatBase::checkExtraOptions
	 */
	public function testCheckExtraOptionsAcceptsEmptyString(): void {
		$extraOpts = [];
		$instance = $this->getTestableInstance( [], $extraOpts );

		// Should not throw an exception for empty string
		$instance->exposedCheckExtraOptions( '' );

		$this->assertTrue( true );
	}

	/**
	 * @covers SparqlResultFormatBase::checkExtraOptions
	 */
	public function testCheckExtraOptionsAcceptsEmptyArray(): void {
		$extraOpts = [];
		$instance = $this->getTestableInstance( [], $extraOpts );

		// Should not throw an exception for empty array
		$instance->exposedCheckExtraOptions( [] );

		$this->assertTrue( true );
	}

	/**
	 * @covers SparqlResultFormatBase::jsRegisterFunction
	 */
	public function testJsRegisterFunctionGeneratesCorrectJsCode(): void {
		$instance = $this->getTestableInstance();
		$launch = 'myFunction(config);';

		$result = $instance->exposedJsRegisterFunction( $launch );

		$this->assertStringContainsString( 'window.sparqlResultFormatsElements', $result );
		$this->assertStringContainsString( 'window.sparqlResultFormatsElements.push', $result );
		$this->assertStringContainsString( 'config:config', $result );
		$this->assertStringContainsString( 'start:function(config)', $result );
		$this->assertStringContainsString( 'myFunction(config);', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::jsRegisterFunction
	 */
	public function testJsRegisterFunctionIncludesLaunchCode(): void {
		$instance = $this->getTestableInstance();
		$launch = 'customLauncher.init(config, options);';

		$result = $instance->exposedJsRegisterFunction( $launch );

		$this->assertStringContainsString( 'customLauncher.init(config, options);', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::jsRegisterFunction
	 */
	public function testJsRegisterFunctionStructure(): void {
		$instance = $this->getTestableInstance();
		$launch = 'test();';

		$result = $instance->exposedJsRegisterFunction( $launch );

		// Verify the structure of the JS code
		$this->assertStringContainsString( 'if (!window.sparqlResultFormatsElements)', $result );
		$this->assertStringContainsString( 'window.sparqlResultFormatsElements = [];', $result );
	}

	/**
	 * @covers SparqlResultFormatBase::getName
	 */
	public function testGetNameReturnsName(): void {
		$instance = $this->getTestableInstance();
		$instance->setName( 'TestFormat' );

		$this->assertSame( 'TestFormat', $instance->getName() );
	}

	/**
	 * @covers SparqlResultFormatBase::getDescription
	 */
	public function testGetDescriptionReturnsDescription(): void {
		$instance = $this->getTestableInstance();
		$instance->setDescription( 'Test description' );

		$this->assertSame( 'Test description', $instance->getDescription() );
	}

	/**
	 * @covers SparqlResultFormatBase::getParams
	 */
	public function testGetParamsReturnsParams(): void {
		$params = [
			'param1' => [ 'type' => 'string' ],
			'param2' => [ 'type' => 'boolean' ],
		];
		$instance = $this->getTestableInstance( $params );

		$this->assertSame( $params, $instance->getParams() );
	}

	/**
	 * @covers SparqlResultFormatBase::getExtraOptions
	 */
	public function testGetExtraOptionsReturnsExtraOpts(): void {
		$extraOpts = [
			'opt1' => [ 'description' => 'Option 1' ],
			'opt2' => [ 'description' => 'Option 2' ],
		];
		$instance = $this->getTestableInstance( [], $extraOpts );

		$this->assertSame( $extraOpts, $instance->getExtraOptions() );
	}

	/**
	 * @covers SparqlResultFormatBase::getQueryStructure
	 */
	public function testGetQueryStructureReturnsQueryStructure(): void {
		$instance = $this->getTestableInstance();
		$instance->setQueryStructure( 'SELECT ?s ?p ?o WHERE { ?s ?p ?o }' );

		$this->assertSame( 'SELECT ?s ?p ?o WHERE { ?s ?p ?o }', $instance->getQueryStructure() );
	}

	/**
	 * @covers SparqlResultFormatBase::getComplexTypes
	 */
	public function testGetComplexTypesReturnsComplexTypes(): void {
		$instance = $this->getTestableInstance();
		$complexTypes = [ 'type1', 'type2' ];
		$instance->setComplexTypes( $complexTypes );

		$this->assertSame( $complexTypes, $instance->getComplexTypes() );
	}
}

/**
 * Testable subclass that exposes protected methods for testing.
 */
class TestableSparqlResultFormatBase extends SparqlResultFormatBase {

	public function __construct( array $params = [], array $extraOpts = [] ) {
		$this->params = $params;
		$this->extraOpts = $extraOpts;
	}

	public function setName( string $name ): void {
		$this->name = $name;
	}

	public function setDescription( string $description ): void {
		$this->description = $description;
	}

	public function setQueryStructure( string $queryStructure ): void {
		$this->queryStructure = $queryStructure;
	}

	public function setComplexTypes( array $complexTypes ): void {
		$this->complexTypes = $complexTypes;
	}

	public function exposedGetParameterValue( $options, $paramName, $defaultValue, $asBoolean = false ) {
		return $this->getParameterValue( $options, $paramName, $defaultValue, $asBoolean );
	}

	public function exposedGetSparqlEndpointBasicAuthString( $endpointData ) {
		return $this->getSparqlEndpointBasicAuthString( $endpointData );
	}

	public function exposedCheckExtraOptions( $extra ) {
		return $this->checkExtraOptions( $extra );
	}

	public function exposedJsRegisterFunction( $launch ) {
		return $this->jsRegisterFunction( $launch );
	}
}

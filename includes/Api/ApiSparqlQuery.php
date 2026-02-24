<?php

namespace MediaWiki\Extension\SparqlResultFormat\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;
use Wikimedia\ParamValidator\ParamValidator;

class ApiSparqlQuery extends ApiBase {

	public function execute() {
		$params = $this->extractRequestParams();
		$endpointName = $params['endpointName'];
		$query = $params['query'];

		$config = MediaWikiServices::getInstance()->getMainConfig();
		$endpointDefinitions = $config->get( 'SparqlEndpointDefinition' );

		if ( !isset( $endpointDefinitions[$endpointName] ) ) {
			$this->dieWithError(
				[ 'apierror-sparqlresultformat-endpoint-not-found', $endpointName ],
				'endpoint-not-found'
			);
		}

		$ep = $endpointDefinitions[$endpointName];
		$sparqlEndpoint = $ep['url'];
		$connectionTimeout = $ep['connectionTimeout'] ?? 10;
		$requestTimeout = $ep['requestTimeout'] ?? 30;
		$sslVerify = $ep['verifySSLCertificate'] ?? true;

		$user = null;
		$password = null;
		if ( isset( $ep['basicAuth'] ) ) {
			$user = $ep['basicAuth']['user'] ?? null;
			$password = $ep['basicAuth']['password'] ?? null;
		}

		$result = $this->executeSparqlQuery(
			$sparqlEndpoint,
			$query,
			$connectionTimeout,
			$requestTimeout,
			$sslVerify,
			$user,
			$password
		);

		if ( $result['error'] ) {
			$this->dieWithError(
				[ 'apierror-sparqlresultformat-query-failed', $result['error'] ],
				'query-failed',
				[ 'httpcode' => $result['httpCode'] ]
			);
		}

		$this->getResult()->addValue( null, 'sparqlresult', $result['data'] );
	}

	private function executeSparqlQuery(
		string $endpoint,
		string $query,
		int $connectionTimeout,
		int $requestTimeout,
		bool $sslVerify,
		?string $user,
		?string $password
	): array {
		$post = [ 'query' => $query ];
		$fieldsString = http_build_query( $post );

		$ch = curl_init( $endpoint );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fieldsString );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $connectionTimeout );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $requestTimeout );
		curl_setopt( $ch, CURLOPT_VERBOSE, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $sslVerify );

		if ( $user !== null && $password !== null ) {
			curl_setopt( $ch, CURLOPT_USERPWD, "$user:$password" );
		}

		$headers = [
			'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
			'Accept: application/sparql-results+json',
			'User-Agent: SparqlResultFormat MediaWiki Extension'
		];
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		$response = curl_exec( $ch );
		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$error = curl_errno( $ch ) ? curl_error( $ch ) : null;
		curl_close( $ch );

		if ( $error ) {
			return [ 'error' => $error, 'httpCode' => 0, 'data' => null ];
		}

		if ( $httpCode >= 400 ) {
			return [ 'error' => "HTTP $httpCode", 'httpCode' => $httpCode, 'data' => null ];
		}

		$data = json_decode( $response, true );
		return [ 'error' => null, 'httpCode' => $httpCode, 'data' => $data ];
	}

	public function getAllowedParams() {
		return [
			'endpointName' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
			],
			'query' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
			],
		];
	}

	public function needsToken() {
		return false;
	}

	public function isReadMode() {
		return true;
	}

	protected function getExamplesMessages() {
		return [
			'action=sparqlquery&endpointName=myEndpoint&query=SELECT * WHERE { ?s ?p ?o } LIMIT 10'
				=> 'apihelp-sparqlquery-example-1',
		];
	}
}

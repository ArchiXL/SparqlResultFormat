<?php
/**
 * Stub classes for MediaWiki dependencies used in standalone unit tests.
 * These allow code coverage analysis without requiring the full MediaWiki environment.
 */

if ( !class_exists( 'Parser' ) ) {
	class Parser {
		public $registeredHooks = [];

		public function setFunctionHook( $id, $callback, $flags = 0 ) {
			$this->registeredHooks[$id] = $callback;
			return true;
		}
	}
}

if ( !class_exists( 'SpecialPage' ) ) {
	class SpecialPage {
		protected $mName;

		public function __construct( $name = '', $restriction = '' ) {
			$this->mName = $name;
		}

		protected function setHeaders() {
		}

		protected function outputHeader( $summaryMessageKey = '' ) {
		}

		protected function getOutput() {
			return new StubOutputPage();
		}
	}
}

if ( !class_exists( 'StubOutputPage' ) ) {
	class StubOutputPage {
		public function addHTML( $html ) {
		}

		public function addModules( $modules ) {
		}
	}
}

if ( !function_exists( 'wfMessage' ) ) {
	function wfMessage( $key, ...$params ) {
		return new StubMessage( $key, $params );
	}
}

if ( !class_exists( 'StubMessage' ) ) {
	class StubMessage {
		private $key;
		private $params;

		public function __construct( $key, $params = [] ) {
			$this->key = $key;
			$this->params = $params;
		}

		public function __toString() {
			return $this->key;
		}

		public function text() {
			return $this->key;
		}

		public function parse() {
			return $this->key;
		}
	}
}

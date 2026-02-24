<?php
/**
 * PHPUnit bootstrap file for standalone unit tests.
 * Loads MediaWiki stubs before the autoloader to allow coverage of files
 * that depend on MediaWiki classes.
 */

// Load MediaWiki stubs first
require_once __DIR__ . '/stubs/MediaWikiStubs.php';

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

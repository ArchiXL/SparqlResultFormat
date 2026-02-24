# SparqlResultFormat

[![CI](https://github.com/imolainformatica/SparqlResultFormat/actions/workflows/ci.yml/badge.svg)](https://github.com/imolainformatica/SparqlResultFormat/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/imolainformatica/SparqlResultFormat/branch/main/graph/badge.svg)](https://codecov.io/gh/imolainformatica/SparqlResultFormat)
[![MediaWiki](https://img.shields.io/badge/MediaWiki-%3E%3D1.43-blue.svg)](https://www.mediawiki.org/wiki/MediaWiki)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.1-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

SparqlResultFormat is a MediaWiki extension that allows you to query SPARQL endpoints and visualize the results in various formats including tables, charts, graphs, and treemaps.

## Features

- **Multiple output formats:**
  - Tables (sortable, with CSV export)
  - Bar charts
  - Pie charts
  - Donut charts
  - Bubble charts
  - Treemaps (D3.js)
  - Network graphs (Cytoscape.js)
  - CSV download

- **Parser functions** for embedding SPARQL visualizations in wiki pages
- **API module** for programmatic SPARQL queries
- **Basic authentication** support for protected endpoints
- **Customizable styling** and configuration options

## Requirements

| Requirement | Version |
|-------------|---------|
| MediaWiki   | >= 1.43 |
| PHP         | >= 8.1  |

## Installation

1. Download and place the `SparqlResultFormat` directory in your MediaWiki `extensions` folder.

2. Add the following to your `LocalSettings.php`:

```php
wfLoadExtension( 'SparqlResultFormat' );

// Configure your SPARQL endpoints
$wgSparqlEndpointDefinition = [
    'myEndpoint' => [
        'url' => 'https://example.org/sparql',
        // Optional: basic authentication
        'basicAuth' => [
            'user' => 'username',
            'password' => 'password'
        ],
        // Optional: timeouts
        'connectionTimeout' => 10,
        'requestTimeout' => 30,
        'verifySSLCertificate' => true
    ]
];
```

3. Run `php maintenance/update.php` to update the database (if needed).

## Usage

### Parser Functions

```wiki
{{#sparql2table:
  sparqlEndpoint=myEndpoint
  |sparql=SELECT ?name ?value WHERE { ?s rdfs:label ?name ; :value ?value }
  |tableClass=wikitable sortable
}}

{{#sparql2barchart:
  sparqlEndpoint=myEndpoint
  |sparql=SELECT ?category ?count WHERE { ... }
  |divStyle=width:600px;height:400px;
}}

{{#sparql2graph:
  sparqlEndpoint=myEndpoint
  |sparql=SELECT ?source ?target WHERE { ?source :relatedTo ?target }
  |layout=dagre
}}
```

### Available Parser Functions

| Function | Description |
|----------|-------------|
| `#sparql2table` | Render results as a sortable HTML table |
| `#sparql2barchart` | Render results as a bar chart |
| `#sparql2piechart` | Render results as a pie chart |
| `#sparql2donutchart` | Render results as a donut chart |
| `#sparql2bubblechart` | Render results as a bubble chart |
| `#sparql2treemap` | Render results as a D3.js treemap |
| `#sparql2graph` | Render results as a Cytoscape.js network graph |
| `#sparql2csv` | Provide a CSV download link |

## Documentation

Full documentation is available on the **Special:SparqlResultFormat** page after installation.

## Development

### Running Tests

```bash
composer install
composer phpunit
```

### Code Style

```bash
composer test
```

## License

[MIT](LICENSE.md)

## Authors

- Gabriele Cornacchia
- Matteo Busanelli

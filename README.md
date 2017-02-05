Graphite GraphBuilder
=====================

[![Latest Stable Version](https://img.shields.io/packagist/v/ondram/graphite-graph-php.svg?style=flat-square)](https://packagist.org/packages/ondram/graphite-graph-php)
[![Build Status](https://img.shields.io/travis/OndraM/graphite-graph-php.svg?style=flat-square)](https://travis-ci.org/OndraM/graphite-graph-php)
[![License](https://img.shields.io/packagist/l/ondram/graphite-graph-php.svg?style=flat-square)](https://packagist.org/packages/ondram/graphite-graph-php)

GraphBuilder is a DSL and ini-based templating language for PHP to assist in constructing URL query strings for use with [Graphite](http://graphiteapp.org/) graphs.

About
-----

[Graphite](http://graphiteapp.org/) provides several interfaces for creating graphs and dashboards,
but one of its powerful features is an [render API](https://graphite.readthedocs.io/en/latest/render_api.html) for generating graphs
and retrieving raw data. This allows easy embedding of graphs in custom
dashboards and other applications.

The process of describing complex graphs is however cumbersome at best.
GraphBuilder attempts to reduce the complexity of embedding
Graphite graphs in PHP based applications by providing a fluent API for
describing graphs and a facility for loading full or partial graph
descriptions from ini files.


Examples
--------

```php
$graphUrl = Graphite\GraphBuilder::builder()
    ->title('Memory')
    ->vtitle('MiB')
    ->width(800)
    ->height(600)
    ->from('-2days')
    ->buildSeries('memory-free')
        ->cactiStyle()
        ->color('green')
        ->alias('Free')
        ->scale(1 / (1024 * 1024)) // B to MiB
        ->build()
    ->build()
    ;

echo '<img src="http://graphite.example.com/render?' . $graphUrl . '">';
```

For more usage examples see files in [examples/](https://github.com/OndraM/graphite-graph-php/tree/master/examples) directory.


Credits
-------
Originally written by [Bryan Davis](http://bd808.github.com/) with support from [Keynetics](http://keynetics.com/).

Updated to use PHP namespaces etc. by [Ondrej Machulda](https://github.com/OndraM).

Inspired by https://github.com/ripienaar/graphite-graph-dsl/.

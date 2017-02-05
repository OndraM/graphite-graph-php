<?php

namespace Graphite\Graph;

use Graphite\ConfigurationException;
use Graphite\GraphBuilder;

/**
 * DSL for building Graphite series to display on a graph.
 *
 * One or more series are passed to the Graphite server as <var>target</var>
 * parameters in a render request. Each series is a description of the data to
 * graph and instructions on how to render it. There are a large number of
 * functions which can be applied to combine, manipulate and decorate
 * collections of data points which are either loaded from a datasource such
 * as whisper or an RRD file or generated by a function.
 *
 * Graphite uses a nested function call syntax to describe each series.
 * Manging this by hand for a reasonably complex rendering can become quite
 * tedious. This DSL presents a fluid syntax for describing each series as
 * though it were a chain of transforms to be applied to an object.
 *
 * <code>
 * <?php
 *  $series = Series::builder('whisper.metric.name')
 *      ->cactistyle()
 *      ->color('green')
 *      ->alias('Free')
 *      ->scale(1 / (1024 * 1024)) // B to MiB
 *      ->build();
 * </code>
 *
 * Although this builder can be used as a stand-alone component, it will most
 * often be used via a call to {@link GraphBuilder::buildSeries()} as
 * part of a larger graph creation process:
 * {@example series_example.php}
 *
 * @author Bryan Davis <bd808@bd808.com>
 * @copyright 2012 Bryan Davis and contributors. All Rights Reserved.
 * @license http://www.opensource.org/licenses/BSD-2-Clause Simplified BSD License
 * @link https://graphite.readthedocs.io/en/latest/functions.html
 *
 * @method $this alias(string $newName)
 * @method $this aliasByNode(int ...$node)
 * @method $this aliasSub(string $search, string $replace)
 * @method $this alpha(float $value)
 * @method $this areaBetween()
 * @method $this asPercent(float $total = null)
 * @method $this averageAbove(float $n)
 * @method $this averageBelow(float $n)
 * @method $this averageSeries()
 * @method $this averageSeriesWithWildcards(int ...$position)
 * @method $this cactiStyle()
 * @method $this color(string $theColor)
 * @method $this cumulative()
 * @method $this currentAbove(float $n)
 * @method $this currentBelow(float $n)
 * @method $this dashed(float $value = null)
 * @method $this derivative()
 * @method $this diffSeries(string ...$name)
 * @method $this divideSeries(string $divisorSeries)
 * @method $this drawAsInfinite()
 * @method $this exclude(string $pattern)
 * @method $this group(string ...$name)
 * @method $this groupByNode(int $nodeNum, string $callback)
 * @method $this highestAverage(float $value)
 * @method $this highestCurrent(float $value)
 * @method $this highestMax(float $value)
 * @method $this hitcount(string $intervalString)
 * @method $this holtWintersAberration(float $delta = 3)
 * @method $this holtWintersConfidenceArea(float $delta = 3)
 * @method $this holtWintersConfidenceBands(float $delta = 3)
 * @method $this holtWintersForecast()
 * @method $this integral()
 * @method $this keepLastValue()
 * @method $this legendValue(string ...$valueType)
 * @method $this limit(float $value)
 * @method $this lineWidth(float $width)
 * @method $this logarithm(float $base = 10)
 * @method $this lowestAverage(float $value)
 * @method $this lowestCurrent(float $value)
 * @method $this maximumAbove(float $value)
 * @method $this maximumBelow(float $value)
 * @method $this maxSeries(string ...$name)
 * @method $this minimumAbove(float $value)
 * @method $this minSeries(string ...$name)
 * @method $this mostDeviant(float $value)
 * @method $this movingAverage(float $value)
 * @method $this movingMedian(float $value)
 * @method $this multiplySeries(string ...$name)
 * @method $this nonNegativeDerivative(float $maxValue = null)
 * @method $this nPercentile(float $value)
 * @method $this offset(float $factor)
 * @method $this percentileOfSeries(float $value, string $interpolate = 'False')
 * @method $this rangeOfSeries(string ...$name)
 * @method $this removeAbovePercentile(float $value)
 * @method $this removeAboveValue(float $value)
 * @method $this removeBelowPercentile(float $value)
 * @method $this removeBelowValue(float $value)
 * @method $this scale(float $factor)
 * @method $this scaleToSeconds(float $seconds)
 * @method $this secondYAxis()
 * @method $this smartSummarize(string $intervalString, string $func = 'sum')
 * @method $this sortByMaxima()
 * @method $this sortByMinima()
 * @method $this stacked()
 * @method $this stdev(float $points, float $windowTolerance = 0.1)
 * @method $this substr(int $start = 0, int $stop = 0)
 * @method $this summarize(string $intervalString, string $func = 'sum', string $alignToFrom = 'False')
 * @method $this sumSeries(string ...$name)
 * @method $this sumSeriesWithWildcards(int ...$position)
 * @method $this timeShift(string $timeShift)
 * @method $this transformNull(float $default = 0)
 * aliases:
 * @method $this avg() Alias for averageSeries()
 * @method $this cacti() Alias for cactiStyle()
 * @method $this centile(float $value) Alias for nPercentile()
 * @method $this counter(float $maxValue = null) Alias for nonNegativeDerivative()
 * @method $this impulse() Alias for drawAsInfinite()
 * @method $this inf() Alias for drawAsInfinite()
 * @method $this max(string ...$name) Alias for maxSeries()
 * @method $this min(string ...$name) Alias for minSeries()
 * @method $this null(float $default = 0) Alias for transformNull()
 * @method $this sum(string ...$name) Alias for sumSeries()
 */
class Series
{
    /**
     * Series configuration data.
     *
     * @var array
     */
    protected $conf;
    /**
     * Paired graph builder.
     *
     * @var GraphBuilder
     */
    protected $graph;

    /**
     * Constructor.
     *
     * @param string $series Base series to construct series from.
     * @param null|mixed $graph
     */
    public function __construct($series = null, $graph = null)
    {
        $this->conf = [];
        if (null !== $series) {
            $this->conf['series'] = $series;
        }
        $this->graph = $graph;
    }

    /**
     * Handle attempts to call non-existant methods.
     *
     * Looks up method name as a function and if found adds that function and
     * the given arguments to this builder's configuration.
     *
     * If no matching function was found it will attempt to lookup the given
     * method as a generator and add it to the current configuration.
     *
     * @param string $name Method name
     * @param array $args Invocation arguments
     * @return Series Self, for method chaining
     * @see Functions
     * @see Generators
     */
    public function __call($name, $args)
    {
        // TODO: actually pull the call spec and verify that the required number
        // of arguments have been supplied.
        $func = Functions::canonicalName($name);
        if (false !== $func) {
            $this->conf[$func] = $args;
        } else {
            $gen = Generators::canonicalName($name);
            if (false !== $gen) {
                $this->conf[':generator'] = $name;
                if ($args) {
                    $this->conf[$name] = $args;
                }
            }
        }

        return $this;
    }

    /**
     * Get the description of this series as an array suitable for use with a
     * call to {@link GraphBuilder::metric()}.
     *
     * @return array Series configuration
     */
    public function asMetric()
    {
        return $this->conf;
    }

    /**
     * Build this series.
     *
     * Returns either an array of series configuration data or the string
     * representation of this series depending on whether or not this series has
     * a GraphBuilder or not.
     *
     * @return mixed Series parameter for use in query string or parent graph
     * @see generate()
     * @see asMetric()
     */
    public function build()
    {
        if (null === $this->graph) {
            return self::generate($this);
        }

        return $this->graph->series('', $this->asMetric());
    }

    /**
     * Builder factory.
     *
     * @param string $series Base series to construct series from.
     * @return Series Series builder
     */
    public static function builder($series = null)
    {
        return new self($series);
    }

    /**
     * Generate the target parameter for a given configuration.
     *
     * @param mixed $conf Configuration as array or Series object
     * @throws ConfigurationException If neither series nor target is set in $conf
     * @return string Series parameter for use in query string
     */
    public static function generate($conf)
    {
        if ($conf instanceof self) {
            $conf = $conf->conf;
        }

        if (isset($conf['target'])) {
            // explict target has been provided by the user
            return $conf['target'];
        }

        $haveAlias = false;

        if (isset($conf[':generator'])) {
            $name = $conf[':generator'];
            $spec = Generators::callSpec($name);
            if (null !== $spec && (
                    isset($conf[$name]) || (
                        1 == $spec->requiredArgs() &&
                        CallSpec::argIsString($spec->getArg(0))
                    )
                )
            ) {
                // args are explicit or we can use the alias
                $args = (isset($conf[$name])) ? $conf[$name] : $conf['alias'];
                if (is_scalar($args)) {
                    $args = [$args];
                }

                // override series with generated content
                $conf['series'] = $spec->asString('', $args);
                $haveAlias = $spec->isAlias();
            }
        }

        if (!isset($conf['series'])) {
            throw new ConfigurationException(
                'metric does not have any data associated with it.'
            );
        }

        if (is_array($conf['series'])) {
            // generate each grouped series
            $grouped = [];
            foreach ($conf['series'] as $series) {
                $grouped[] = self::generate($series);
            }
            $conf['series'] = implode(',', $grouped);
        }

        // find functions named in the conf data
        $funcs = [];
        foreach ($conf as $key => $args) {
            $name = Functions::canonicalName($key);
            if ($name) {
                $funcs[$name] = $args;
            }
        }
        // sort the found functions by priority
        uksort($funcs, [Functions::class, 'cmp']);

        // start from the provided series
        $target = $conf['series'];

        // build up target string
        foreach ($funcs as $name => $args) {
            $spec = Functions::callSpec($name);

            if (is_scalar($args)) {
                $args = [$args];
            }

            if ($spec->isAlias() && $haveAlias) {
                // only one alias should be applied in each target
                continue;
            } elseif ($spec->isAlias() && !$args[0]) {
                // explicitly disabled alias
                continue;
            }

            // format call as a string
            $target = $spec->asString($target, $args);

            // keep track of alias application
            $haveAlias = $haveAlias || $spec->isAlias();
        }

        return $target;
    }
}

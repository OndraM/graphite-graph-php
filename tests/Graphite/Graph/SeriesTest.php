<?php
/**
 * @package Graphite
 * @subpackage Graph
 */

/**
 * @package Graphite
 * @subpackage Graph
 */
class Graphite_Graph_SeriesTest extends PHPUnit_Framework_TestCase {

  /**
   * Given: a moderately complex DSL usage
   * Expect: a well formed query string
   */
  public function test_first_alias_wins () {
    $spec = array(
        "series" => "something.prod.*.requests.count",
        "aliasByNode" => 3,
        "cactistyle" => true,
        "alias" => "App Hits"
      );
    $this->assertEquals(
        'cactiStyle(aliasByNode(something.prod.*.requests.count,3))',
        Graphite_Graph_Series::generate($spec));
  } //end testFirstAliasWins


  /**
   * Given: a wildcard function and a truthy ini value
   * Expect: no args other than series to function call
   */
  public function test_wildcard_no_args () {
    $spec = array(
        "series" => "something.prod.*.requests.count",
        "sum" => '1',
      );
    $this->assertEquals(
        'sumSeries(something.prod.*.requests.count)',
        Graphite_Graph_Series::generate($spec));
  }

} //end Graphite_Graph_SeriesTest

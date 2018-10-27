# CHANGELOG for 2.x
This changelog references the relevant changes done in 2.x versions.


## v2.0.2
* Update mapping such that `text` types using pbj_keyword `analyzer` are now `keyword` types using pbj_keyword `normalizer`.


## v2.0.1
* Update `Gdbots\Pbj\Exception\GdbotsPbjException` to extend `\Throwable`.


## v2.0.0
__BREAKING CHANGES__

* Update `Gdbots\Pbj\Marshaler\Elastica\*` classes to use `"ruflin/elastica": "~5.3"`.
* Change `pbj_keyword_analyzer` to just `pbj_keyword` in `Gdbots\Pbj\Marshaler\Elastica\MappingFactory`.
* Require php `>=7.1` in `composer.json`.
* __NOTICE:__ php7 type hinting (scalar arguments and return types) with `declare(strict_types=1);`
  will be added in a minor update in the 2.x line. This should NOT be a breaking change if your 
  code is respecting the doc blocks which represent the public API.

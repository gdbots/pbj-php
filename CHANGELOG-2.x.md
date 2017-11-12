# CHANGELOG for 2.x
This changelog references the relevant changes done in 2.x versions.


## v2.0.0
__BREAKING CHANGES__

* Update `Gdbots\Pbj\Marshaler\Elastica\*` classes to use `"ruflin/elastica": "~5.3"`.
* Change `pbj_keyword_analyzer` to just `pbj_keyword` in `Gdbots\Pbj\Marshaler\Elastica\MappingFactory`.
* Require php `>=7.1` in `composer.json`.

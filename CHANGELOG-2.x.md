# CHANGELOG for 2.x
This changelog references the relevant changes done in 2.x versions.


## v2.2.1
* Use `DateTimeImmutable` in `DateType` and `DateTimeType`. 


## v2.2.0
* Optimize the `MessageResolver` by using a manifest with all of the classes and mixins so no messages have to be instantiated when using `findOneUsingMixin` or `findAllUsingMixin` to find out if they have the given mixin.
* Comment out the logic that includes fields on serialized or marshaled objects when field is null.


## v2.1.5
* Allow `IdentifierType` to be 255 bytes, same as `StringType`.


## v2.1.4
* Revert set dynamic to `false` by default. It seems to break the dynamic templates which are named "dynamic" but in fact are rather explicit. Needs more testing to determine the sweet spot.


## v2.1.3
* Set dynamic to `false` by default when using the Elastica MappingFactory. https://www.elastic.co/guide/en/elasticsearch/reference/current/dynamic.html


## v2.1.2
* Allow for `beberlei/assert` constraint `^2.7 || ^3.0`.
* Add php 7.3 to travis.


## v2.1.1
* Apply same guard rules for all string types so format and pattern are enforced. This is needed because it is very common to need a text field type for a URL due to sizes often being greater than 255 bytes.


## v2.1.0
__REQUIRES REINDEXING__

Using the MappingFactory to generate the mappings in Elasticsearch will cause some mappings to change.  You will need to recreate the mappings and reindex your data when you upgrade to this version.

* Update mapping such that `text` types using pbj_keyword `analyzer` are now `keyword` types using pbj_keyword `normalizer`.
* Add `getCustomNormalizers` method to `Gdbots\Pbj\Marshaler\Elastica\MappingFactory` which by default provides the pbj_keyword normalizer.


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

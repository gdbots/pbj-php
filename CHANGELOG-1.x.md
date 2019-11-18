# CHANGELOG for 1.x
This changelog references the relevant changes done in 1.x versions.


## v1.2.0
* Require php `>=7.1`.
* Add `MessageResolver::registerManifest` from v2.x. 


## v1.1.5
* issue #40: Create shortcut in Mixin for findOne and findAll.


## v1.1.4
* issue #38: Validate message type against anyOf only, not class name.
* Update reference to class names to use php's builtin class property, e.g. `MyClass::class`.


## v1.1.3
* issue #36: BUG :: In `MessageRef`, when using php serialization a MessageRef doesn't always restore curies correctly.
  This may have been the culprit for issue #34.


## v1.1.2
* issue #34: BUG :: MessageRef from string fails when no tag is supplied.
* Extended `\JsonSerializable` in `Gdbots\Pbj\WellKnown\Identifier` since we implement it on all identifiers anyways.
* Added `MessageResolver::resolveQName` which returns the `SchemaCurie` for the given `SchemaQName`.


## v1.1.1
* issue #32: Add "TrinaryType".  ref https://en.wikipedia.org/wiki/Three-valued_logic


## v1.1.0
__POSSIBLE BREAKING CHANGES__

If you are using `BigNumber`, `GeoPoint`, `Identifier` or `Microtime` classes in your own code to populate schema fields 
you must use the new `Gdbots\Pbj\WellKnown\*` classes instead.  The old classes in `gdbots/common` are now deprecated.

* issue #30: Add WellKnown Types.


## v1.0.2
* issue #26: Use `pbj_keyword_analyzer` for strings with patterns in `Gdbots\Pbj\Marshaler\Elastica\MappingFactory`.
* Added `Schema::createMessage` convenience method that creates a message instance. 


## v1.0.1
* issue #26: Use `object` instead of `nested` when message field is not a list in `Gdbots\Pbj\Marshaler\Elastica\MappingFactory`.


## v1.0.0
__BREAKING CHANGES__

* Mixins moved to `gdbots/schemas` library and in namespace according to category (Command, Event, etc.) not `Mixin`.
* `MessageTrait` removed as it only provided typehinting fixes in your IDE and those are no longer needed.
* issue #22: Add support for AWS 3.x and Elastic 3.x
* Removed `Format::DATED_SLUG` option, just use `SLUG` and enforce slashes/dated option in your app.
  `Format::SLUG` now enforces this regex `^([\w\/-]|[\w-][\w\/-]*[\w-])$`.
* Renamed `MessageCurie` to `SchemaCurie` and added `SchemaQName` for the ultra compact reference.
* `MessageResolver` methods `resolveSchemaId`, `resolveMessageCurie` to `resolveId` and `resolveCurie` respectively.

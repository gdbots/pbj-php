# CHANGELOG for 1.x
This changelog references the relevant changes done in 1.x versions.


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

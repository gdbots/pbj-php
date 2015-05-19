# CHANGELOG for 0.x
This changelog references the relevant changes done in 0.x versions.


## v0.3.0
* issue #13: Adding `isInSet`, `isInList`, `isInMap` to [Message] and implemented in [AbstractMessage].
* issue #13: For convenience, added `getFromListAt` and `getFromMap` to [Message] and implemented in [AbstractMessage].
* issue #12: Added [ItemMarshaler] for DynamoDb items for "aws/aws-sdk-php" library.
* issue #11: Added [DocumentMarshaler] and [MappingFactory] for "ruflin/elastica" library.
* [BooleanType] Modified to not be `allowedInSet`.
* Removed interface and abstract class implementation from mixins.  Mixins must only provide fields and a trait for convenience.
  It is up to the concrete message to implement interfaces and use those traits.  Compiler/generator will handle this eventually.
* Now using psr4 for less nesting of directories.


## v0.2.2
* Rename composer package to `gdbots/pbj`.


## v0.2.1
* issue #8: [EntityMixin] Make the type for `_id` an `IdentifierType` and use a random uuid as default.
* issue #8: [Entity] Changed entity id field name from `id` to `_id` and removed `setEntityId` from interface.
* issue #6: [PhpArraySerializer] Do not try to deserialize a null value, instead clear the field.
* issue #5: [MessageRef] The `id` will now support any string matching `/^[A-Za-z0-9:_\-]+$/`.
* issue #4: [Field] TimestampType default can now be disabled with `useTypeDefault = false`.
* [Format] Added "hashtag" option and implemented check in [StringType].


## v0.2.0
* issue #1: [Type] Add `isMessage` method to interface for simple check to determine if fields contain a nested message.
* issue #1: [MessageType] Impements `anyOfClassNames` so a field can support an array of possible messages.
* issue #2: [Type] Removed `allowedInSetOrList` and added `allowedInSet` since all field rules except `set` support all types.
* [Message] Removed `removeFromList` method from interface and abstract message.
* [Message] Added `removeFromListAt` method to interface and abstract message.
* [MessageRef], [MessageRefType] Added new class for creating links/references to other messages.
* Removed all correl_id fields from extensions and hasCorrelId, getCorrelId, setCorrelId, in favor of `correlator`.
* Killed all `Extension` classes and converted them to `Mixins`.
* [Schema] Eliminated schema extension capability, must use mixins now as `Schema` is now marked as final.
* [Schema] `create` method removed, use the constructor instead.
* [Field] When the field type is a `Message` the className option can be a class or interface.
* Added formats `slug` and `dated-slug` options for string type.
* Added [IdentifierType] which supports class names for explicit identifiers.


## v0.1.0
* Initial version.

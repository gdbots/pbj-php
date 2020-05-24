# CHANGELOG for 3.x
This changelog references the relevant changes done in 3.x versions.


## v3.0.0
__BREAKING CHANGES__

* Require php `>=7.4`
* Uses php7 type hinting throughout with `declare(strict_types=1);`
* Uses `"ramsey/uuid": "^4.0"`
* Replaces `moontoast/math` with `brick/math`
* Moves `Gdbots\Pbj\WellKnown\MessageRef` to `Gdbots\Pbj\WellKnown\MessageRef`
* Removes `Gdbots\Pbj\WellKnown\BigNumber`, we are just using `BigInt` direct from `brick/math` now.
* Removes `Mixin` and `AbstractMixin` since the `gdbots/pbjc` no longer uses them.
* Changes `MessageResolver::findOneUsingMixin` and `MessageResolver::findAllUsingMixin` to use a curie (string) for resolution and instead of returning the schema it just returns curies (strings) of the messages using the mixin.
* Removes `Gdbots\Pbj\Exception\HasEndUserMessage`.
* Changes `MessageResolver::register` to accept a map of curie to classname since mixin resolution uses separate manifests.
* Simplifies `Schema` so the mixins are just the curies in string form, not objects.
* Adds `Gdbots\Pbj\WellKnown\NodeRef` and `Gdbots\Pbj\Type\NodeRefType`.
* Removes use of `gdbots/common` lib as those classes moved to `Gdbots\Pbj\Util\*Util`.
* Removes use of `Gdbots\Common\FromArray` and `Gdbots\Common\ToArray` interfaces as it wasn't really needed.
* Adds `Gdbots\Pbj\Enum` which replaces `Gdbots\Common\Enum`.
* Adds `Codec::skipValidation` which allows an optimized process for encoding/decoding that uses native php scalars/arrays where possible instead of objects. This should only be used when data is very trusted and highest performance possible is required.
* Replaces `Gdbots\Pbj\Marshaler\Elastica\MappingFactory` with `Gdbots\Pbj\Marshaler\Elastica\MappingBuilder` which produces a single mapping (for 7.x) from one or more schemas since elasticsearch no longer uses types.

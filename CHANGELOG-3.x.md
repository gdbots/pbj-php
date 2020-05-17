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
* Removes `MessageResolver::findOneUsingMixin` and `MessageResolver::findAllUsingMixin`.  Mixin lookup resolution no longer used and the mixin mostly disappears once schema is compiled.
* Removes `Gdbots\Pbj\Exception\HasEndUserMessage`.
* Change `MessageResolver::register` to simply accept map of curie to classname since mixin resolution going away means we don't need a manifest.
* Simplifies `Schema` so the mixins are just the curies in string form, not objects.
* Adds `Gdbots\Pbj\WellKnown\NodeRef` and `Gdbots\Pbj\Type\NodeRefType`.
* Removes use of `gdbots/common` lib as those classes moved to `Gdbots\Pbj\Util\*Util`.
* Removes all use of `Gdbots\Common\FromArray` and `Gdbots\Common\ToArray` interfaces as it wasn't really needed.
* Adds `Gdbots\Pbj\Enum` which replaces `Gdbots\Common\Enum`.

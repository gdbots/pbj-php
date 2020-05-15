# CHANGELOG for 3.x
This changelog references the relevant changes done in 3.x versions.


## v3.0.0
__BREAKING CHANGES__

* Require php `>=7.4`
* Moves `Gdbots\Pbj\WellKnown\MessageRef` to `Gdbots\Pbj\WellKnown\MessageRef`
* Uses php7 type hinting throughout with `declare(strict_types=1);`
* Uses `"ramsey/uuid": "^4.0"`
* Replaces `moontoast/math` with `brick/math`
* Removes `Mixin` and `AbstractMixin` since the `gdbots/pbjc` no longer uses them.
* Removes `MessageResolver::findOneUsingMixin` and `MessageResolver::findAllUsingMixin`.  Mixin lookup resolution no longer used and the mixin mostly disappears once schema is compiled.
* Simplifies `Schema` so the mixins are just the curies in string form, not objects.

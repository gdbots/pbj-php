# CHANGELOG for 1.x
This changelog references the relevant changes done in 1.x versions.


## v1.0.0
__BREAKING CHANGES__

* Mixins moved to `gdbots/pbj-schemas-php` library and in namespace according to category (Command, Event, etc.) not `Mixin`.
* `MessageTrait` removed as it only provided typehinting fixes in your IDE and those are no longer needed.
* issue #22: Add support for AWS 3.x and Elastic 3.x

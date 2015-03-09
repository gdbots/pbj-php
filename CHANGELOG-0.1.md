CHANGELOG for 0.1.x
===================

This changelog references the relevant changes (bug and security fixes) done in 0.1 minor versions.

* 0.1.1 (2015-03-07)

 * #1: [Type] Add `isMessage` method to interface for simple check to determine if fields contain a nested message.
 * #1: [MessageType] Impements `anyOfClassNames` so a field can support an array of possible messages.
 * #2: [Type] Removed `allowedInSetOrList` and added `allowedInSet` since all field rules except `set` support all types.
 * [Message] Removed `removeFromList` method from interface and abstract message.
 * [Message] Added `removeFromListAt` method to interface and abstract message.
 * [MessageRef], [HasMessageRef], [MessageRefType] Added new class for creating links/references to other messages.
 * Removed all correl_id fields from extensions and hasCorrelId, getCorrelId, setCorrelId, in favor of `correlator`.

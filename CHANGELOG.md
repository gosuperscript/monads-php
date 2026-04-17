# Changelog

All notable changes to `gosuperscript/monads` will be documented in this file.

## v1.1.1 - 2026-04-17

### What's Changed

* Add Writer monad implementation with comprehensive tests by @robertvansteen in https://github.com/gosuperscript/monads-php/pull/17
* fix: use `Exporter::export(...)` instead of `$this->export()` in `IsOk` and `IsErr` by @joelbutcher in https://github.com/gosuperscript/monads-php/pull/18

### New Contributors

* @joelbutcher made their first contribution in https://github.com/gosuperscript/monads-php/pull/18

**Full Changelog**: https://github.com/gosuperscript/monads-php/compare/v1.1.0...v1.1.1

## v1.1.0 - 2026-02-03

### What's Changed

* Add comprehensive README documentation by @Copilot in https://github.com/gosuperscript/monads-php/pull/13
* feat: pass Throwable on Result::expect by @erikgaal in https://github.com/gosuperscript/monads-php/pull/16

**Full Changelog**: https://github.com/gosuperscript/monads-php/compare/v1.0.0...v1.1.0

## v1.0.0 - 2025-09-23

### Added

- [`Lazy`](src/Lazy/Lazy.php), a type to delay execution to a later stage
- [`Option`](src/Option/Option.php), a type to represent an optional value
- [`Result`](src/Result/Result.php), a type used for returning and propagating errors

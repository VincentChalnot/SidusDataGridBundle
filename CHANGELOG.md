# Changelog

All notable changes to this project will be documented in this file.

## [4.0.2] - 2026-07-21

### Fixed

- PHP 8.4 deprecates typed parameters defaulting to `null` without an explicit `?` prefix.
  Fixed in `Model\DataGrid`.

### Compatibility

- Verified against PHP 8.2-8.4, Symfony 7.4, `sidus/filter-bundle` ^6.0.

### Note

- The above fix was originally released as `4.0.1`. Its tag was mistakenly force-moved after
  publication (a `git commit --amend` to add this changelog file, then a re-tag) to attach this
  changelog entry, which Packagist correctly rejected: stable tags are immutable once published.
  `v4.0.1` has been restored to its originally-published commit (`c410d7a6`, no changelog file),
  and this changelog entry is released as `4.0.2` instead. No code changed between `4.0.1` and
  `4.0.2` - `4.0.2` only adds this file.

## [4.0.0] - 2023-09-01

See git history prior to this file for changes up to 4.0.0.

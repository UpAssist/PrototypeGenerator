# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.1.0](https://github.com/UpAssist/PrototypeGenerator/compare/1.0.0...1.1.0) (2026-11-19)


### Bug Fixes

* `generator:prototype` crashed on Neos 9 (`NodeType::getName()` no longer exists)
* the package broke the Flow proxy build on Neos 8 (it required the Neos 9 `ContentRepositoryRegistry`)

### Features

* support Neos 8.3 and 9.x from one release: node types are read through the Neos 9 Content Repository when present and the Neos 8 `NodeTypeManager` otherwise
* new setting `UpAssist.PrototypeGenerator.contentRepository` (Neos 9 only, default `default`)
* constrain `neos/flow` and `neos/neos` to `^8.3 || ^9.0` instead of `*`

## [1.0.0](https://github.com/UpAssist/PrototypeGenerator/compare/0.3.1...1.0.0) (2025-08-27)

### [0.3.1](https://github.com/UpAssist/PrototypeGenerator/compare/0.3.0...0.3.1) (2024-03-31)


### Bug Fixes

* wrong rendering of PropType definition ([62a2ec9](https://github.com/UpAssist/PrototypeGenerator/commit/62a2ec93fa86ebcb7431f6ac24f0d5bf8c2c32a8))

## [0.3.0](https://github.com/UpAssist/PrototypeGenerator/compare/0.2.2...0.3.0) (2023-07-13)

### [0.2.2](https://github.com/UpAssist/PrototypeGenerator/compare/0.2.1...0.2.2) (2023-03-21)

### [0.2.1](https://github.com/UpAssist/PrototypeGenerator/compare/0.2.0...0.2.1) (2023-03-21)


### Bug Fixes

* rendering of properties based on a preset render type not set error ([716e104](https://github.com/UpAssist/PrototypeGenerator/commit/716e10440a550f0b50bda1ae9e77a0b1d622f541))

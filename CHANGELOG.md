# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [3.3.0] - 2024-02-07

### Changed

- The code further complies with (WordPress) coding standards.

Full set of changes: [`3.2.4...3.3.0`][3.3.0]

[3.3.0]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/v3.2.4...v3.3.0

## [3.2.4] - 2023-10-13

### Commits

- Added `ninja-forms-save-progress` for https://github.com/pronamic/wp-pronamic-pay-ninjaforms/issues/8. ([118b910](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/118b910a662684845f8b5221ed99b6e9983516bd))
- Added extra Ninja Forms extensions for testing features. ([50fa9b8](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/50fa9b83b9a937ce04295edc15ef360549201de7))
- Use new `pronamic/wp-env-quick-login` library. ([e27cdcd](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/e27cdcd34ec243d958661930c0fb4ea42e6c2aa1))
- Auto setup after wp-env start. ([ab9314f](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/ab9314f17efc3cc483a02da3c39c72d39d1388b5))

Full set of changes: [`3.2.3...3.2.4`][3.2.4]

[3.2.4]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/v3.2.3...v3.2.4

## [3.2.3] - 2023-06-01

### Commits

- Switch from `pronamic/wp-deployer` to `pronamic/pronamic-cli`. ([164e796](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/164e7962e904abcce23ec5fb9e288c6cf779384b))

Full set of changes: [`3.2.2...3.2.3`][3.2.3]

[3.2.3]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/v3.2.2...v3.2.3

## [3.2.2] - 2023-03-27

### Commits

- Set Composer type to WordPress plugin. ([159c033](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/159c033491ae296a8bb6f13f19a413fbb589ba90))
- No longer use `\pronamic_pay_plugin()->get_pages()`. ([794a80f](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/794a80f1b1f908f7fa20a5908678f3bc4d125d8d))
- Created .gitattributes ([726c751](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/726c751d0379744074b2b328f023fd04a215bc5c))
- Requires PHP: 7.4. ([1c0fff0](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/1c0fff0762cdd8caddb01ee3473a6e507aa8902b))

Full set of changes: [`3.2.1...3.2.2`][3.2.2]

[3.2.2]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/v3.2.1...v3.2.2

## [3.2.1] - 2023-01-31
### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`3.2.0...3.2.1`][3.2.1]

[3.2.1]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/v3.2.0...v3.2.1

## [3.2.0] - 2022-12-23

### Commits

- Added "Requires Plugins" header. ([11eb54d](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/11eb54d579dfe39caa0cbf0fbc3c0031717f7d1c))
- No longer use deprecated `FILTER_SANITIZE_STRING`. ([9f926c4](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/commit/9f926c4e265408c563c1548ca2007dd7be5c2536))

### Composer

- Changed `php` from `>=5.6.20` to `>=8.0`.
- Changed `wp-pay/core` from `^4.4` to `v4.6.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v3.1.1
Full set of changes: [`3.1.1...3.2.0`][3.2.0]

[3.2.0]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/v3.1.1...v3.2.0

## [3.1.1] - 2022-09-27
- Update to `wp-pay/core` version `^4.4`.

## [3.1.0] - 2022-09-26
- Updated for new payment methods and fields registration.

## [3.0.1] - 2022-02-16
- Fixed delaying all actions ([#4](https://github.com/pronamic/wp-pronamic-pay-ninjaforms/issues/4)).

## [3.0.0] - 2022-01-10
### Changed
- Updated to https://github.com/pronamic/wp-pay-core/releases/tag/4.0.0.
- Cleanup Ninja Forms session once payment has been fulfilled.

## [2.0.0] - 2021-08-05
- Updated to `pronamic/wp-pay-core`  version `3.0.0`.
- Updated to `pronamic/wp-money`  version `2.0.0`.
- Changed `TaxedMoney` to `Money`, no tax info.
- Switched to `pronamic/wp-coding-standards`.

## [1.5.1] - 2021-05-28
- Improved delayed actions.

## [1.5.0] - 2021-04-26
- Added support for delayed actions.

## [1.4.0] - 2021-01-21
- Added gateway configuration setting to form action.

## [1.3.0] - 2021-01-14
- Removed payment data class.
- Fixed notice payment redirect URL.

## [1.2.0] - 2020-04-03
- Added payment status page action settings.
- Updated action redirect to use payment redirect URL.
- Updated integration dependencies.
- Set plugin integration name.

## [1.1.0] - 2020-03-19
- Fix incorrect selected payment method in payment methods fields when editing entry.
- Extension extends abstract plugin integration.

## [1.0.3] - 2019-12-22
- Improved error handling with exceptions.

## [1.0.2] - 2019-08-26
- Updated packages.

## [1.0.1] - 2019-04-15
- Fix form builder not loading due to removed 'pricing' field type section since Ninja Forms 3.4.6.
- Workaround Ninja Forms not passing plugin default currency setting correctly.

## 1.0.0 - 2018-05-16
- First release.

[unreleased]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/3.1.1...HEAD
[3.1.1]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/3.0.1...3.1.0
[3.0.1]: https://github.com/pronamic/wp-pronamic-pay-ninjaforms/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/2.0.0...3.0.0
[2.0.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.5.0...2.0.0
[1.5.1]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.4.0...1.5.0
[1.4.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-extensions/ninjaforms/compare/1.0.0...1.0.1

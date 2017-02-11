# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [v0.3] - 11-02-2016
### Added
- Added a changelog

### Changed
- Changed the paginate function as show a pagination does not belong to a model.
- Removed wp_pagenavi from the suggestions as it is not included in the model anymore.
- Changed the variables in the models from protect to public because of possible & allowed usage outside of the model.

## [v0.2.1] - 08-02-2016
### Fixed
- Fixed a typo in the namespacing and the autoloader.

## [v0.2] - 06-02-2016
### Added
- Added a class UserModel/User to get all users.
- Added a class UserModel/Subscriber to get all the subscriber users.
- Added a class TermModel/Category to get all the category terms.
- Added requirements and suggestions to the composer file.

### Changed
- Changed the namespacing of the models.

[Unreleased]: https://github.com/DannyvanHolten/WordPress-Models/compare/v0.3...develop
[v0.3]: https://github.com/DannyvanHolten/WordPress-Models/compare/v0.2.1...v0.3
[v0.2.1]: https://github.com/DannyvanHolten/WordPress-Models/compare/v0.2...v0.2.1
[v0.2]: https://github.com/DannyvanHolten/WordPress-Models/compare/v0.1...v0.2
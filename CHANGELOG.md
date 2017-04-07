# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.8.1] - 04-04-2017
### Changed 
* Changed the post excerpt function to cut words instead of characters

## [0.7] - 29-03-2017
### Added
* Changed the fields function so it also accepts permalink, and in stead of an object or an integer. You are able to get the permalink
* Added andWhere function as a placeholder function for where with 'AND' relation

## [0.6] - 08-03-2017
### Added
* Added a getObject function to get the post type object

## [0.5.1] - 07-03-2017
### Changed
* Changed the check for instances of different object to a normal is_object check because of relevanssi casting stdClass objects.

## [0.5] - 03-03-2017
### Added
* Added appendDate function to the postmodel

## [0.4.3] - 03-03-2017
### Changed
* Paged function moved from query function to runQuery function

## [0.4.2] - 21-02-2017
### Fixed
* PostModel now also directly applies the excerpt more filter

## [0.4.1] - 20-02-2017
### Fixed
* WP trim excerpt doesn't actually trim the excerpt. PostModel now works directly with the excerpt length filter

## [0.4] - 17-02-2017
### Added
* Add an apply filters function after the query is run in the runQuery function

## [0.3] - 11-02-2017
### Added
* Added a changelog

### Changed
* Changed the paginate function as show a pagination does not belong to a model.
* Removed wp_pagenavi from the suggestions as it is not included in the model anymore.
* Changed the variables in the models from protect to public because of possible & allowed usage outside of the model.

## [0.2.1] - 08-02-2017
### Fixed
- Fixed a typo in the namespacing and the autoloader.

## [0.2] - 06-02-2017
### Added
* Added a class UserModel/User to get all users.
* Added a class UserModel/Subscriber to get all the subscriber users.
* Added a class TermModel/Category to get all the category terms.
* Added requirements and suggestions to the composer file.

### Changed
* Changed the namespacing of the models.

[Unreleased]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.8...develop
[0.8.1]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.8...0.8.1
[0.8]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.7...0.8
[0.7]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.6...0.7
[0.6]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.5.1...0.6
[0.5.1]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.5...0.5.1
[0.5]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.4.3...0.5
[0.4.3]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.4.2...0.4.3
[0.4.2]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.4.1...0.4.2
[0.4.2]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.4...0.4.1
[0.4.1]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.3...0.4
[0.3]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.2.1...0.3
[0.2.1]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.2...0.2.1
[0.2]: https://github.com/DannyvanHolten/WordPress-Models/compare/0.1...0.2
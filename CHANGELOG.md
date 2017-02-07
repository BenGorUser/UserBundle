#CHANGELOG

This changelog references the relevant changes done between versions.

To get the diff for a specific change, go to https://github.com/BenGorUser/UserBundle/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/BenGorUser/UserBundle/compare/v0.7.0...v0.8.0

##v0.8.1
* Removed HTTP exceptions from Api controllers and added JsonResponse as return objects.

##v0.8.0
* Added Api integration with json render responses apart of the html render responses.
* Added two Symfony console commands that purge outdated invitation tokens and remember password tokens.
* Removed deprecated JWT Authenticator.
* Rewritten the UserUrlGenerator's associated service.
  * More info in the new documentation's event subscriber section and inside UPGRADE.md file.
* Upgraded PHP-CS Fixer.
* Fixed bug that generate a infinite loop with Twig template includes.
* [Travis CI] Dropped support for HHVM and added for PHP 7.1.

##v0.7.4
* Changed JwtController response code from 404 to 400.

##v0.7.3
* Catch UserEmailInvalidException in JwtController.

##v0.7.2
* Fixed dependencies for framework-bundle 3.2

##v0.7.1
* Deprecated JWT Authenticator
* Now JWTController responses return JsonResponses.

##v0.7.0
* Added JWT authentication
* Now the CLI commands are always enabled to simplify the user experience
* Changed `success_redirection_route` strategy for logins

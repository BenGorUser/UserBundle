#CHANGELOG

This changelog references the relevant changes (bug and security fixes) done in 0.x minor versions.

To get the diff for a specific change, go to https://github.com/BenGorUser/UserBundle/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/BenGorUser/UserBundle/compare/v0.6.0...v0.7.0

##v0.7.1

* Deprecated JWT Authenticator
* Now JWTController responses return JsonResponses.

##v0.7.0

* Added JWT authentication
* Now the CLI commands are always enabled to simplify the user experience
* Changed `success_redirection_route` strategy for logins

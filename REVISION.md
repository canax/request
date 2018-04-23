Revision history
=================================

Notes for development v1.1.0*
---------------------------------

* Check organisation of tests.
* OWASP testing.
* Move globals to own class and include COOKIE and FILES.
* Globals support getting by array.
* Move getCurrentUrl to Uri or UriBuilder.
* Move extractRoute to Uri/UriBuilder.
* Move parts of init() to be supported by Uri/UriBuilder.
* Check if support FIG on Uri interface.



v1.1.* (2018-04-23)
---------------------------------

* Added Codacy badge.
* Adding service in config/di/request.php.



v1.1.0 (2018-04-23)
---------------------------------

* Enhance dockblock comments.
* Make pass scrutinizer by installing phpunit.
* Update travis to pass >= php 7.0.
* Remove composer.lock.
* Upgrade CircleCI to v2.
* Upgrade composer.json to >= php 7.0.
* Add Codeclimate badge.
* Dockerize repo.
* Upgrade phpunit testcase to PHP >= 7.0.



v1.0.6 (2017-10-15)
---------------------------------

* Add setBody to ease unit testing.



v1.0.5 (2017-06-26)
---------------------------------

* Added testcase for rawurldecode.
* Adding `getBody()` to retrieve the body of the HTTP request.



v1.0.4 (2017-04-03)
---------------------------------

* Decode incoming url to fix encoded %-characters.



v1.0.3 (2017-03-30)
---------------------------------

* Clean up docblocks.
* Merged fix for nginx SERVER_NAME versus HTTP_HOST, #1.
* Adding Sensiolabs badge.



v1.0.2 (2017-03-13)
---------------------------------

* Adding request method.
* Change name of request class, removed basic.



v1.0.1 (2017-03-07)
---------------------------------

* Cleanup makefile.



v1.0.0 (2017-03-03)
---------------------------------

* Extracted from anax to be its own module.

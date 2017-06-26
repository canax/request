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

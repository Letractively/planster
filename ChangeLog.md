## v0.4.0.1 (17 Sep 2007) ##

  * Fixes a CSS issue which would make the PLANster link unclickable
  * Put the message area to a reasonable position

## v0.4.0 (31 Aug 2007) ##

  * Updated the database layout (see README.upgrade)
  * Should now work with PHP 5
  * Added note about apache mod\_rewrite and MultiViews to README
  * Upgraded smarty to version 2.6.18
  * Upgraded Prototype to version 1.5.1.1
  * Upgraded script.aculo.us to version v1.7.1\_beta3
  * Replaced the JavaScript JSON implementation with Prototype's functionality
  * Added support for google ads
  * Fixed some layout issues in Konqueror
  * Fixed a JavaScript issue that would cause Safari and Konqueror to crash
  * The category-selector is now hidden if there are no categories available
  * Added the ability to count answers
  * The installer now checks for apache and PHP (database) modules
  * Fixed ADOdb detection in setup script
  * Updated the skin
  * Small changes to the feed
  * Bumped libdesire version
  * Thanks to Florian Ostermair for his various suggestions

## v0.3.0 (16 Mar 2007) ##

  * Now using JSON for data transport (see README.upgrade)
  * Items can now be categorized, eg. to offer several time slots at some date
  * Recreated large parts of the UI
  * People, dates and categories can now be deleted by giving them empty names
  * Using UTF-8 now in order to get rid of encoding issues
  * Fixed some PHP complaints
  * Cleaner interface between JavaScript and PHP
  * Major code cleanup (JavaScript and PHP)
  * Removed the tooltips
  * Replaced icons with Mark James' Silk icon set 1.3
  * Updated the documentation (README and README.upgrade)
  * Fixed a typo in the ChangeLog :)

## v0.2.2 (19 May 2006) ##

  * Made inviting/adding people more clear
  * Fixed a caching issue with IE6
  * Made db-field sizes configurable and limited input field sizes accordingly
  * Dynamically requested input fields now receive focus when displayed

## v0.2.1 (06 May 2006) ##

  * Improved invitations UI
  * Added an option to clone an event
  * Added a javascript calendar when adding/modifying dates
  * Made the RDF feed's time representation RFC 2822 compliant
  * New dates could not be added in non-js browsers (fixed)
  * Dates could not be moved in non-js browsers (fixed)
  * Some internal cleanup

## v0.2.0 (26 Apr 2006) ##

  * Added a cron script to erase expired events
  * Slightly changed some templates
  * Implemented invitations
  * Added RDF feeds to track changes to events

## v0.1.1 (12 Apr 2006) ##

  * Events with insufficient data would be saved despite the warning to
> > fill in all required values (fixed).
  * Some database fields were too small (fixed).
  * Event creators now receive an e-mail message with information about the
> > newly created event.

## v0.1.0 (11 Apr 2006) ##

  * Initial public release
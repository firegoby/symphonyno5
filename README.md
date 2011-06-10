# Symphony No.5 #

* Version 0.1.8
* Date: 10th June 2011

## Overview

Symphony No.5 is an adapted HTML5Boilerplate for use with Symphony CMS, Less CSS & Coffeescript

## Changelog

* 0.1.8 - 10 Jun 11 - Reverting closing tag of fallback JQuery, XSLT doesn't like it
* 0.1.7 - 10 Jun 11 - 
  * Added installation gudie
  * Upgraded to JQuery 1.6.1
  * Upgraded to Modernizr 2.0
  * Simpler closing script tag for fallback JQuery imported from HBP5
  * Updated humans.txt
  * Removed default home.xsl (unnecessary)
  * Removed all known remaining personalisation
* 0.1.6 - 21 May 11 - Stripped out personalisation of humans.txt
* 0.1.5 - 20 May 11 - Updating to new .oldie class for IE compatibility (from HTML5Bolierplate)
* 0.1.4 - 17 May 11 - Added Coffeescript support to the Ant build script (Coffeescript required)
* 0.1.3 - 16 May 11 - Fixed bug in the JQuery local fallback script closing tag 
* 0.1.2 - 13 May 11 - Fixed bug in the IE9/non-IE conditional comment CDATA
* 0.1.1 - 28 Apr 11 - Updated to Symphony 2.2.1
* 0.1.0 - 31 Mar 11 - Initial version 

## Features

* HTML5 master.xsl template
* Ant build script to minify & concatenate javascripts in single production file
* Ant build script compiles Coffeescript files 
* Less CSS folder/file setup with HTML5 Reset and bootstrap.less (use with Less.app)
* A lot of the HTML5Boilerplate goodness!

## Non Symphony CMS Requirements

* Less.app (or other LESS CSS compilation option)
* Coffeescript (install via node.js & npm

## Installation (unix command line)

* `cd` to the directory of your choice and execute the following commands, replacing `projectname` with your own
  * `git clone https://github.com/firegoby/symphonyno5.git projectname`
  * `cd projectname`
  * `git remote rename origin symphony`
  * `git submodule update --init`
  * `chmod 777 symphony .`
  * `chmod -R 777 workspace`
* Setup your MySQL database
* Open `http://yourproject.domain/install.php` (yourproject.domain depends on your local development setup)
* After installation remember to tighten file permissions again: -
  * `chmod 755 symphony .`
  * `chmod -R 775 manifest`
  * `chmod -R 775 workspace`
* And delete the installation script
  * `rm install.php`

## Symphony CMS Overview

Symphony is a `PHP` & `MySQL` based CMS that utilises `XML` and `XSLT` as
its core technologies. 

* Visit the website at <http://symphony-cms.com/>
* Github Repository: <http://github.com/symphonycms/symphony-2/>

### Symphony Server Requirements

- PHP 5.2 or above
- PHP's LibXML module, with the XSLT extension enabled (--with-xsl)
- MySQL 4.1 or above
- An Apache or Litespeed webserver
- Apache's mod_rewrite module or equivalent


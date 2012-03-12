# Symphony No.5 #

* Version 0.3.0
* Date: 12th March 2012

## Overview

Symphony No.5 is an adapted [HTML5Boilerplate](http://html5boilerplate.com/) for use with [Symphony CMS](http://symphony-cms.com/), [Less CSS](http://lesscss.org/) & [Coffeescript](http://coffeescript.org/) and featuring [Bootstrap, from Twitter](http://twitter.github.com/bootstrap/). It aims to be a constantly evolving, pragmatic best practices starting point for new Symphony CMS projects.

## Features

* **HTML5** master.xsl template based on HTML5Boilerplate
* Ant build script to **minify & concatenate javascripts** into a single production file
* Ant build script compiles any **Coffeescript** files before concatenation 
* **Less CSS** - Sensible defaults folder/file setup with: -
  * **HTML5 Boilerplate** styles, including normalize.css
  * **Bootstrap** from Twitter
  * Basic separation of responsibilities: files for **mixins**, **variables**, **main** & **responsive**
  * All Less **stylesheets compile into a single (minified) styles.css** file for production

## Changelog

* 0.3.0 - 12 Mar 12 -
  * Updated to HTML5Boilerplate 3.0
  * Added Bootstrap, from Twitter (2.0.1)
  * Added default script.coffee to js/ dir
  * Added sensible default files structure to less/ dir
  * Improved Symphony CMS installation instructions re: file permissions
* 0.2.2 - 16 Nov 11 - Updated to Symphony 2.2.5
* 0.2.1 - 23 Oct 11 - Updated to Symphony 2.2.4
* 0.2.0 - 22 Aug 11 - Updating to H5BP 2.0, fix for build script & misc bug fixes
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

## Non Symphony CMS Requirements

* Less.app (or other LESS CSS compilation option)
* Coffeescript (install via node.js & npm)

## Installation (unix command line)

1. `cd` to the directory of your choice and execute the following commands, replacing `projectname` with your own
2. `git clone https://github.com/firegoby/symphonyno5.git projectname`
3. `cd projectname`
4. `git remote rename origin symphony`
5. `git submodule update --init`
6. `chmod 777 symphony .`
7. `chmod -R 777 workspace`
8. Setup your MySQL database
9. Open `http://yourproject.domain/install.php` (yourproject.domain depends on your local development setup)

### File Permissions
Once installed tighten up the file/dir permissions (**IMPORTANT**: Please make sure you *understand* what is happening here and don't just blindly cut-n-paste. Refer to the [Symphony CMS Forum](http://symphony-cms.com/discuss/) for more information and help).

1. `find . -type d -exec chmod 755 {} \;`
2. `find . -type f -exec chmod 644 {} \;`
3. `rm install.php install.sql workspace/install.sql update.php install-log.txt`

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

## Licenses

Please refer to the LICENSE.* files in the root directory.

# Symphony No.5

* Version 0.10.0
* Date: 1st October 2014

## Overview

Symphony No.5 is a [Symphony CMS](http://getsymphony.com/) installation with an adapted [HTML5Boilerplate](http://html5boilerplate.com/) `master.xsl` template. It works in tandem with [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) for command-line admin of your Symphony project.

## Features

* **HTML5** master.xsl XSLT template based on the best of HTML5Boilerplate
* Works with [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) for **easy command-line admin**
* `extensions.csv` list for Symphony Extensions - installed as git submodules via [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git)
* **Split Manifest directory structure** for separate Development & Production environments
* Repsonsive Design Test Page with common breakpoints (/responsive.html)
* `sections` folder in `workspace` for modular structuring of XSLT ([more](http://www.getsymphony.com/discuss/thread/96648/))
* **XSLResourceLoader** extension for loading of DataSources & Events within XSLT ([more](http://www.getsymphony.com/discuss/thread/90092/))
* **Symphony-Pimms** ready - easy installation of Gulp-based build system [Symphony-Pimms](https://github.com/firegoby/symphony-pimms) with a great selection of features!

## Contents

* [**Installation**](#installation)
* [Frequently Asked Questions](#frequently-asked-questions)
* [**Changelog**](#changelog)
* [**Symphony CMS Overview**](#symphony-cms-overview)
  * [Symphony Server Requirements](#symphony-server-requirements)
* [**Licenses**](#licenses)

## Installation

1. Install [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) to your $PATH (or local to your project)
2. Setup Apache (or equivalent) web server to serve your domain
3. Setup a MySQL database (recommend charset=`utf-8`, collate=`utf8_unicode_ci`)
4. Setup a MySQL user for the database, taking note of connection details
4. `symphonyno5 new projectname` and follow the instructions

### Frequently Asked Questions

1. **What happened to all the CSS & Javascript minification stuff?**

  In order to be more modular this functionality has been moved to a dedicated Gulp build system [Symphony-Pimms](https://github.com/firegoby/symphony-pimms)- check it out!

2. **Why have you included a non-core extension? (XSLResourceLoader)**

  I don't want to start adding lots of default extensions but **XSLResourceLoader** allows a much greater degree of flexibility by removing data-source and event dependency listings from the database and putting them in files under version control. Until pages are stored as XML (coming in Symphony 2.x?) this is the next best approach and thus I feel worthy of inclusion as a default.

3. **I don't like Feature X, can you remove it?**

  I try to keep this fairly unopinionated but in order for it to be useful some choices have to made. If it diverges too much from what you need you can always just fork the project and tailor it to your exact needs. Updating the repository URLs in the [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) command-line script would even allow you to install your custom version from the command line.

## Changelog
* 0.10.0 - 1 Oct 14
  * Updated to Symphony v2.5.1
  * Updated to jQuery v2.1.1
  * Removed last vestiges of default CSS & Javascript, super seceded by Gulp build system [Symphony-Pimms](https://github.com/firegoby/symphony-pimms)
  * Removed IE conditional comments - develop for the modern '[Evergreen](https://support.google.com/a/answer/33864?hl=en)' browsers unless there's specific reason not to.
  * Removed FontAwesome - use SVGs, much better sub-pixel antialiasing
  * Removed Modernizr - add a custom version as/when it's needed
* 0.9.1 - 13 Aug 13
  * Added `sections` folder in `workspace` for modular structuring of XSLT ([more](http://www.getsymphony.com/discuss/thread/96648/))
  * Added `XSLResourceLoader` extension for loading of DataSources & Events within XSLT ([more](http://www.getsymphony.com/discuss/thread/90092/))
* 0.9.0 - 9 Aug 13 -
  * Upgraded to Symphony v2.3.3
  * Upgraded to JQuery v1.10.2
  * Upgraded to HTML5Boilerplate v4.2.0
  * Upgraded to Modernizr v2.6.2
  * Upgraded to Normalize.css v2.1.2
  * Upgraded to FontAwesome v3.2.1
  * Removed HTML5Boilerplate separate print.css stylesheet
* 0.8.0 - 3 Dec 12 -
  * Removed all asset minification support (moved to separate extension [Asset Compiler](http://github.com/firegoby/asset_compiler)
  * Removed CSS pre-processor defaults
  * Added `normalize.css` as CSS library option
  * Added H5BP's default `h5bp-main.css` as CSS library option
* 0.7.0 - 24 Nov 12 -
  * Add automated `production-{SHA1hash}.min.ext` styles and scripts
  * Updates to match improvements in [SymphonyNo5.sh](http://github.com/firegoby/symphonyno5.sh.git)
  * Remove legacy Ant build script
  * Remove custom Less CSS code, make CSS PreProcessor support agnostic
  * Remove Bootstrap integration
  * Remove hana+nils XSLT Kit for Symphony
  * Remove use-case specific meta tags
* 0.6.1 - 22 Nov 12 -
  * Add [Production Mode](http://github.com/firegoby/production_mode.git) extension for automatic serving of minified assets in production mode
* 0.6.0 - 21 Nov 12 -
  * New command-line bash script for easy installation/admin [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git)
  * Move extension/submodule control to `extensions.csv`
  * Move apple-touch-icon(s) out of root directory
  * Remove .htaccess - let Symphony create it as part of install
* 0.5.3 - 20 Nov 12 -
  * Added [hana+nils' XSLT Kit for Symphony](http://github.com/hananils/kit)
* 0.5.2 - 19 Nov 12 -
  * Upgraded to Symphony v2.3.1
  * Upgraded to JQuery v1.8.3
  * Upgraded to Bootstrap v2.2.1
  * Updated Documentation, added FAQ and more Usage instructions
  * Disabled Bootstrap by default - uncomment in main.less, master.xsl & build.xml to selectivly enable features
  * Standardised styles/scripts naming - main.ext and vendor/
  * Moved 3rd party styles to styles/vendor/
* 0.5.1 - 31 Aug 12 -
  * Minor updates from HTML5Boilerplate 4.0.0
* 0.5.0 - 21 Aug 12 -
  * Upgraded to Bootstrap v2.1.0
  * Start adhering to Semantic Versioning for Symphony No.5 releases
* 0.4.1 - 17 Aug 12 -
  * Detailed example installation instructions
  * Less CSS usage instruction
  * Ant build script usage instructions
  * Added Bootstrap javascripts files as active defaults to `master.xsl` and Ant build script
  * Enabled Bootstrap's Responsive features by default in `styles.less`
  * Changed div.container to div.wrapper - compatibility with Bootstrap
  * Removed 'body-' prefix to body.page class, redundant
  * Added 'main' class to each top-level section element
  * Added 'content' class to main page content div
* 0.4.0 - 14 Aug 12 -
  * Upgraded to Symphony CMS 2.3
  * Removed 320andup stylesheets
  * Refactored Bootstrap to run as git submodule, eases upgrading
  * Improved Ant build script to use explicit filelist to allow order-dependent concatenation
  * Improved responsive tester with more sizes and allowing javascript to run
  * Added format-date.xsl XSLT to utilities (more options than date-time.xsl)
  * Added Google Analytics code from H5BP back into master.xsl
  * Merged helper.js into plugins.js
  * Added more .main classes to master.xsl
  * Removed Apple icons meta tags from head in master.xsl
  * Upgraded JQuery 1.7.2 -> 1.8.0
  * Upgraded Modernizr 2.5.3 -> 2.6.1
* 0.3.1 - 7 May 12 -
  * Incorporated much of Andy Clarke's [**320andup**](http://github.com/malarkey/320andup), including: -
     - Font-based icons from [Font Awesome](http://fortawesome.github.com/Font-Awesome)
     - [Selectivizr](http://selectivizr.com/)
     - Responsive design tester
     - LESS file setup, mixins and variables
     - Reference page
  * Merged all Less and CSS files under a single styles/ directory
  * Changed `js/` -> `scripts/`
  * Changed `js/libs/` -> `scripts/vendor/` (H5BP)
  * Changed `js/script.js` -> `scripts/main.js` (H5BP)
  * Changed `js/script.coffee` -> `scripts/main.coffee`
  * Upgraded JQuery 1.7.1 -> 1.7.2
  * Replaced favicon and apple touch icon with custom default
  * Moved multiple resolution apple touch icons to `workspace/images/icons/`
  * Added `.gitignore` files to force empty dirs for `data-sources/`, `events/` & `pages/`
  * Dropped support for IE less than 8
  * Changed all default element IDs to classes in `master.xsl`
  * Added `role="navigation"` to default `nav` element in `navigation.xsl`
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

## Symphony CMS Overview

Symphony is a `PHP` & `MySQL` based CMS that utilises `XML` and `XSLT` as its core technologies.

* Visit the website at [http://getsymphony.com/](http://getsymphony.com/)
* Github Repository: [http://github.com/symphonycms/symphony-2/](http://github.com/symphonycms/symphony-2)

### Symphony Server Requirements

- PHP 5.2 or above
- PHP's LibXML module, with the XSLT extension enabled (--with-xsl)
- MySQL 4.1 or above
- An Apache or Litespeed webserver
- Apache's mod_rewrite module or equivalent

## Licenses

Please refer to the LICENSE.* files in the root directory.

# Symphony No.5

* Version 0.4.1
* Date: 17th August 2012

## Overview

Symphony No.5 is an adapted [HTML5Boilerplate](http://html5boilerplate.com/) for use with [Symphony CMS](http://symphony-cms.com/), [Less CSS](http://lesscss.org/) & [Coffeescript](http://coffeescript.org/) and featuring [Bootstrap, from Twitter](http://twitter.github.com/bootstrap/) and some components from [320andup](http://github.com/malarkey/320andup). It also includes an Ant build script to compile, concatenate & minify javasctipt for production. It aims to be a constantly evolving, pragmatic best practices starting point for new Symphony CMS projects.

## Features

* **HTML5** master.xsl XSLT template based on HTML5Boilerplate
* **Less CSS** styles with: -
  * **Bootstrap** from Twitter
  * **Font Awesome** 
  * Sensible default layout of stylesheets
* All Less **stylesheets compile into a single (minified) styles.css** file for production
* Ant build script to **minify & concatenate javascripts** into a single production file
* Ant build script compiles any **Coffeescript** files before concatenation 
* Ant build script works from a explicit filelist to enable order-dependent concatenation

## Changelog
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
    * Font-based icons from [Font Awesome](http://fortawesome.github.com/Font-Awesome)
    * [Selectivizr](http://selectivizr.com/)
    * Responsive design tester
    * LESS file setup, mixins and variables
    * Reference page
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

## Non Symphony CMS Requirements

* Less.app (or other LESS CSS compilation option)
* Coffeescript (install via node.js & npm)
* Apache Ant (for using the build script)

## Installation

### Basic Installation Instructions

1. Setup Apache & MySQL
2. Clone Symphony No.5 repository
3. Install Symphony CMS

### Detailed Installation Instructions

1. Setup Apache to serve your domain
  * PHP 5.2 or above (PHP 5.3 recommended)
  * PHP's LibXML module, with the XSLT extension enabled (--with-xsl)
  * Apache's mod_rewrite module or equivalent - see .htaccess for details
2. Setup your MySQL database, recommend charset=`utf8`, collate=`utf8_unicode_ci`
3. Setup a MySQL user for database, take note of connection details
4. Clone Symphony No.5 repository
    `git clone git://github.com/firegoby/symphonyno5.git projectname`  
5. cd to the project root
    `cd projectname`
6. init and update the git submodules
    `git submodule update --init`
7. Temporarily loosen file permissions for install
    `chmod -R 777 .`
8. Visit the install directory on the project domain with browser
    `open http://projectname.com/install`
9. Login to Symphony CMS Admin
    `open http://projectname.com/symphony`
10. Agree to deletion of installation files (no longer needed)
11. Set all directories under project root dir to 770 (user & group all, public none)
    `find . -type d -exec chmod 770 {} \;`
    The exact permissions needed will depend on your server configuration, possibly `755` (owner all, group & public read and execute)
12. Set all files to 660 (user & group read-write, public none)
    `find . -type f -exec chmod 660 {} \;`
    Files don't need the executable flag set, alternative permissions could be `644` (owner read/write, group & public read)
13. Switch to bootstrap submodule dir
    `cd workspace/bootstrap`
14. Discard any changes made by above commands
    `git checkout -- .`
15. Checkout the bootstrap version to use (e.g. 2.1.0-wip)
    `git checkout 2.1.0-wip`
16. Commit bootstrap version to git
    `cd ../..`
    `git add workspace/bootstrap`
    `git commit -m "Setting Bootstrap to 2.1.0.-wip"`
17. Post installation cleanup (`git status` should show installation files deleted)
    `git add -u .`
    `git commit -m "Removing installation files"`
18. Remove Symphony No.5 git remote, no longer needed
    `git remote rm origin`

## Usage

### Less CSS Compilation

`workspace/styles/styles.less` is the master stylesheet. Add all other stylesheets as `@import` directives within that files for easy concatenation of all stylesheets into a single production file. If you want to minify the resulting CSS file do so in your LESS compiler, or from the command line (using the official `lessc` compiler available via NPM)
    `lessc --compress workspace/styles/styles.less > workspace/styles/style.css`

### Ant Build Script - Concatenate & Minify Javascript for Production

1. Add all javascripts files (in-order) to `workspace/scripts/build.xml`. If using CoffeeScript add the filename with a `.js` extension, all CoffeeScript files will be compiled before minification and concatenation.
2. `cd workspace/scripts/build`
3. `ant`
4. Alter `master.xsl` to point to the production file `/workspace/scripts/production.min.js`.

#### Notes

The un-minified concatenated `production.js` file is left so that it can be examined in case of errors post-concatenation. If you're concerned about public access to the un-minified version of your scripts delete it before deployment, or block it in .htaccess

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

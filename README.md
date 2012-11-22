# Symphony No.5

* Version 0.6.1
* Date: 22nd November 2012

## Overview

Symphony No.5 is an adapted [HTML5Boilerplate](http://html5boilerplate.com/) for use with [Symphony CMS](http://getsymphony.com/), [Less CSS](http://lesscss.org/) & [Coffeescript](http://coffeescript.org/) and featuring [Bootstrap, from Twitter](http://twitter.github.com/bootstrap/) and some components from [320andup](http://github.com/malarkey/320andup). It works alongside [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) for command-line admin of your Symphony project. It also includes an Ant build script to compile, concatenate & minify javasctipt for production. It aims to be a constantly evolving, pragmatic best practices starting point for new Symphony CMS projects.

## Features

* **HTML5** master.xsl XSLT template based on HTML5Boilerplate
* Works with [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) for easy command-line admin
* `extensions.csv` list for Symphony Extensions - installed as git submodules via [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git)
* Includes the [Production Mode](http://github.com/firegoby/production_mode.git) extension to automatically serve minified assets when in production mode
* **Split Manifest directory structure** for separate Development & Production environments
* **Less CSS** styles with: -
  * **Bootstrap** from Twitter
  * **Font Awesome** 
  * **CSS Reset**
  * **box-sizing: border-box** for all elements
  * Sensible default layout of stylesheets
* All **stylesheets compile into a single (minified) main.css** file for production
* Ant build script to **minify & concatenate javascripts** into a single production file
* Ant build script compiles any **Coffeescript** files before concatenation 
* Ant build script works from a explicit filelist to enable order-dependent concatenation
* [hana+nils' XSLT Kit for Symphony](http://github.com/hananils/kit)
* Repsonsive Design Test Page with common breakpoints (/responsive.html)

## Contents

* [**Installation**](#installation)
  * [Quickstart](#quickstart)
  * [Detailed Installation Instructions](#detailed-installation-instructions)
* [**Usage**](#usage)
  * [Frequently Asked Questions](#frequently-asked-questions)
  * [Less CSS Compilation](#less-css-compilation)
  * [Ant Build Script - Concatenate & Minify Javascript for Production](#ant-build-script--concatenate--minify-javascript-for-production)
* [**Changelog**](#changelog)
* [**Symphony CMS Overview**](#symphony-cms-overview)
  * [Symphony Server Requirements](#symphony-server-requirements)
  * [Non Symphony CMS Requirements](#non-symphony-cms-requirements)
* [**Licenses**](#licenses)

## Installation

### Quickstart

1. Install [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) to your $PATH
2. Setup Apache (or equivalent) web server to serve your domain
3. Setup a MySQL database (recommend charset utf-8)
4. `symphonyno5.sh new projectname`
5. Install Symphony via `http://yourdomain.com/install`
6. `symphonyno5 postinstall`
7. `symphonyno5 fixperms 0775 0664` (*or custom dir & file permissions for your setup*)
8. `git commit -am "Tighten file permissions after installation"`

### Detailed Installation Instructions

1. Install [symphonyno5.sh](http://github.com/firegoby/symphonyno5.sh.git) to your $PATH

2. Setup Apache to serve your domain
  * PHP 5.2 or above (PHP 5.3 recommended)
  * PHP's LibXML module, with the XSLT extension enabled (--with-xsl)
  * Apache's mod_rewrite module or equivalent - see .htaccess for details

3. Setup your MySQL database, recommend charset=`utf8`, collate=`utf8_unicode_ci`

4. Setup a MySQL user for database, take note of connection details

5. `cd` to parent directory that will hold your project (e.g. `cd ~/Sites`)

6. `symphonyno5.sh new projectname`

7. Install Symphony via `http://yourdomain.com/install`

8. After installation, cleanup install files and commit .htaccess and config.php to git
    
    `symphonyno5.sh postinstall`

9. Set the correct dir & file permissions

   **The exact permissions needed will depend on your server configuration and should match those given during Symphony's installation**, by default `0775` and `0664`

   `symphonyno5.sh fixperms 0775 0664`

   Once you've reviewed the permissions changes and are happy, commit them to git

   `git commit -am "Change dir & file permissions to 0775 0664"`


## Usage

### Frequently Asked Questions

1. **How do I enable Bootstrap?**

   Uncomment the @import line(s) in `workspace/styles/main.less` and also any javascript imports you need in the end of `workspace/utilities/master.xsl`. If you're using the Ant build script to produce concatenated and minified javascript for production you'll also want to uncomment the relevant javascripts in the build script `workspace/scripts/build/build.xml`.

2. **I don't want Bootstrap! What do I do?**

   As of version 0.5.2 Bootstrap is disabled by default so you don't need to do anything. If you want to remove all the Bootstrap files then remove the `workspace/bootstrap` git submodule, and delete the references to the javascripts in `workspace/utilities/master.xsl` and in the Ant build script `workspace/scripts/build/build.xml`.

3. **I don't want to use Coffeescript! What do I do?**

   Simply delete `workspace/scripts/main.coffee` and write your javascript in `workspace/scripts/main.js` instead. The Ant build script (`workspace/scripts/build`) will continue to work fine.

4. **I don't want to use Less CSS! What do I do?**

   Just delete the .less files (or all the files in `workspace/styles`) and replace with your plain CSS, SASS, Stylus or whatever stylesheets instead.


### Less CSS Compilation

`workspace/styles/main.less` is the master stylesheet. Add all other stylesheets as `@import` directives within that files for easy concatenation of all stylesheets into a single production file. If you want to minify the resulting CSS file do so in your LESS compiler, or from the command line (using symphonyno5.sh): -
    `symphonyno5.sh compile styles`

### Ant Build Script - Concatenate & Minify Javascript for Production

1. Add all javascripts files (in the order you want them referenced) to `workspace/scripts/build.xml`. If using CoffeeScript add the filename with a `.js` extension, all CoffeeScript files will be compiled before minification and concatenation.
2. `symphonyno5.sh compile scripts`
3. In Symphony admin **Preferences** enable **Production Mode**

#### Note

The un-minified concatenated `production.js` file is left so that it can be examined in case of errors post-concatenation. If you're concerned about public access to the un-minified version of your scripts delete it before deployment, or block it in `.htaccess` or equivalent.

## Changelog

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

### Non Symphony CMS Requirements

* Less.app (or other LESS CSS compilation option)
* Coffeescript (install via node.js & npm) - if using Coffeescript, optional
* Apache Ant (for using the build script)

## Licenses

Please refer to the LICENSE.* files in the root directory.

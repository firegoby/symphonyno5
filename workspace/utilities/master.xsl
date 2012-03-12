<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:include href="date-time.xsl"/>
  <xsl:include href="page-title.xsl"/>
  <xsl:include href="navigation.xsl"/>

  <xsl:variable name="jQueryVersion" select="'1.7.1'"/>

  <xsl:output method="html" encoding="UTF-8" omit-xml-declaration="yes" indent="no" media-type="text/html" />

  <xsl:template match="/">
    <xsl:text disable-output-escaping="yes"><![CDATA[<!doctype html>]]></xsl:text>
    <xsl:comment><![CDATA[[if IE 6 ]><html class="no-js ie6 oldie" lang="en"><![endif]]]></xsl:comment>
    <xsl:comment><![CDATA[[if IE 7 ]><html class="no-js ie7 oldie" lang="en"><![endif]]]></xsl:comment>
    <xsl:comment><![CDATA[[if IE 8 ]><html class="no-js ie8 oldie" lang="en"><![endif]]]></xsl:comment>
    <xsl:comment><![CDATA[[if gt IE 8 ]><!]]></xsl:comment><html class="no-js" lang="en"><xsl:comment><![CDATA[<![endif]]]></xsl:comment>
      <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title><xsl:call-template name="page-title"/></title>
        <meta name="description" content=""/>
        <meta name="author" content=""/>
        <meta name="viewport" content="width=device-width"/>
        <link type="text/plain" rel="author" href="/humans.txt" />
        <link rel="stylesheet" href="/workspace/css/styles.css"/>
        <script src="/workspace/js/libs/modernizr-2.5.3.min.js"></script>
      </head>
      <body class="{$current-page}">
        <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
        <div id="container">
          <header>
            <h1><xsl:value-of select="$website-name"/></h1>
            <xsl:apply-templates select="data/navigation"/>
          </header>
          <div id="main" role="main">
            <xsl:apply-templates/>
          </div>
          <footer>
            <!-- footer content -->
          </footer>
        </div><!-- eo #container -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/{$jQueryVersion}/jquery.min.js"></script>
        <script><![CDATA[window.jQuery || document.write('<script src="/workspace/js/libs/jquery-]]><xsl:value-of select="$jQueryVersion"/><![CDATA[.min.js">\x3C/script>')]]></script>
        <!-- scripts concatenated and minified via ant build script in js/build -->
        <script src="/workspace/js/plugins.js"></script>
        <script src="/workspace/js/script.js"></script>
        <!-- replace 2 lines above with single line below in production -->
        <!--<script src="/workspace/js/production.min.js"></script>-->
        <!-- end concatenated and minified scripts-->
      </body>
    </html>
  </xsl:template>
  <xsl:strip-space elements="*"/><!-- strip whitespace from output -->
</xsl:stylesheet>

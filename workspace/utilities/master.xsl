<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:include href="date-time.xsl"/>
  <xsl:include href="page-title.xsl"/>
  <xsl:include href="navigation.xsl"/>

  <xsl:variable name="buildVersion" select="'1'"/>
  <xsl:variable name="jQueryVersion" select="'1.5.1'"/>

  <xsl:output method="html" encoding="UTF-8" omit-xml-declaration="yes" indent="no" media-type="text/html" />

  <xsl:template match="/">
    <xsl:text disable-output-escaping="yes"><![CDATA[<!doctype html>]]></xsl:text>
    <xsl:comment><![CDATA[[if lt IE 7 ]> <html class="no-js ie6" lang="en"><![endif]]]></xsl:comment>
    <xsl:comment><![CDATA[[if IE 7 ]> <html class="no-js ie7" lang="en"><![endif]]]></xsl:comment>
    <xsl:comment><![CDATA[[if IE 8 ]> <html class="no-js ie8" lang="en"><![endif]]]></xsl:comment>
    <xsl:comment><![CDATA[[if (gte IE 9)|!(IE)]]]></xsl:comment> <html class="no-js" lang="en"><xsl:comment><![CDATA[[endif]]]></xsl:comment>
      <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title><xsl:call-template name="page-title"/></title>
        <meta name="description" content="British Design Studio of Chris Batchelor, providing services worldwide of Web Design &amp; Development, Graphic Design, Photo Restoration, Logo &amp; Brand Design, Domain &amp; Hosting Administration"/>
        <meta name="author" content="Chris Batchelor, Firegoby Design"/>
        <link type="text/plain" rel="author" href="/humans.txt" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link rel="stylesheet" href="/css/style.css?v={$buildVersion}"/>
        <script src="/js/libs/modernizr-1.7.min.js"></script>
      </head>
      <body class="{$current-page}">
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
        <script src="//ajax.googleapis.com/ajax/libs/jquery/{$jQueryVersion}/jquery.js"></script>
        <script><![CDATA[window.jQuery || document.write('<script src="/js/libs/jquery-]]><xsl:value-of select="$jQueryVersion"/><![CDATA[.min.js">%3C/script>')]]></script>
        <!-- scripts concatenated and minified via ant build script in js/build -->
        <script src="/js/plugins.js"></script>
        <script src="/js/script.js"></script>
        <!-- replace 2 lines above with single line below in production -->
        <!--<script src="/js/production.min.js?v={$buildVersion}"></script>-->
        <!-- end concatenated and minified scripts-->
      </body>
    </html>
  </xsl:template>
  <xsl:strip-space elements="*"/><!-- strip whitespace from output -->
</xsl:stylesheet>

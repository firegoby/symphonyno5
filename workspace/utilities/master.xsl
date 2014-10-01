<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:include href="format-date.xsl"/>
  <xsl:include href="page-title.xsl"/>
  <xsl:include href="navigation.xsl"/>

  <xsl:variable name="jQueryVersion" select="'2.1.1'"/>

  <xsl:output method="html" encoding="UTF-8" omit-xml-declaration="yes" indent="no" media-type="text/html" />

  <xsl:template match="/">
    <xsl:text disable-output-escaping="yes"><![CDATA[<!doctype html>]]></xsl:text>
    <html lang="en">
      <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <meta http-equiv="cleartype" content="on" />
        <title><xsl:call-template name="page-title"/></title>
        <meta name="description" content=""/>
        <meta name="author" content=""/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link type="text/plain" rel="author" href="/humans.txt" />
        <link rel="stylesheet" data-compile="true" href="/workspace/styles/main.css"/>
        <link rel="apple-touch-icon" href="/workspace/images/icons/apple-touch-icon.png"/>
      </head>
      <body class="{$current-page}">
        <header class="main masthead" role="banner">
          <h1><a href="/"><xsl:value-of select="$website-name"/></a></h1>
          <xsl:apply-templates select="data/navigation"/>
        </header>

        <div class="main content" role="main">
          <xsl:apply-templates/>
        </div>

        <footer class="main" role="contentinfo">
        </footer>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/{$jQueryVersion}/jquery.min.js"></script>
        <script><![CDATA[window.jQuery || document.write('<script src="/workspace/vendor/jquery-]]><xsl:value-of select="$jQueryVersion"/><![CDATA[.min.js">\x3C/script>')]]></script>
        <script data-compile="true" src="/workspace/scripts/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
      </body>

    </html>
  </xsl:template>
  <xsl:strip-space elements="*"/><!-- strip whitespace from output -->
</xsl:stylesheet>

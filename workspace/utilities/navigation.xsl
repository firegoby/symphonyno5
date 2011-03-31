<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="navigation">
    <nav><ul>
      <xsl:apply-templates select="page[not(types/type='hidden')]"/>
  </ul></nav>
  </xsl:template>

  <xsl:template match="navigation/page">
    <li>
      <a>
        <xsl:choose>
          <xsl:when test="types/type='index'">
            <xsl:attribute name="href"><xsl:value-of select="$root"/>/</xsl:attribute>
          </xsl:when>
          <xsl:otherwise>
            <xsl:attribute name="href"><xsl:value-of select="$root"/>/<xsl:value-of select="@handle"/>/</xsl:attribute>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="@handle=$current-page">
          <xsl:attribute name="class">active</xsl:attribute>
        </xsl:if>
        <xsl:value-of select="name"/>
      </a>
    </li>
  </xsl:template>

</xsl:stylesheet>

<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xpath-default-namespace="">
<!-- xmlns:fn="http://www.w3.org/2005/xpath-functions" -->
<xsl:param name="debug" select="1" as="xs:integer"/>
<xsl:template name="show_square">
<xsl:param name="x" />
<xsl:param name="y" />
<td>
  <xsl:value-of select="$x" />/<xsl:value-of select="$y" />
</td>
</xsl:template>
<xsl:template match="/infos">
<html>
<head>
<title>Map Test</title>
</head>
<body>
<xsl:call-template name="show_square">
  <xsl:with-param name="x" select="13" />
  <xsl:with-param name="y">14</xsl:with-param>
</xsl:call-template>
<!-- <xsl:for-each select="/infos/square[@x=min(12 13)]"> -->
<xsl:for-each select="/infos/square[@x=fn:min(/infos/square/@x)]">
<xsl:sort select="@y" />
<xsl:variable name="firstsquare" select="."/>
<tr>
  <!--
  <xsl:for-each select="/infos/square[@y=$firstsquare/@y]">
  <xsl:sort select="@x" />
  <xsl:call-template name="show_square">
  <xsl:with-param name="x" select="@x" />
  <xsl:with-param name="y" select="@y" />
  </xsl:call-template>
  </xsl:for-each> -->
</tr>
</xsl:for-each>
</body>
</html>
</xsl:template>
</xsl:stylesheet>

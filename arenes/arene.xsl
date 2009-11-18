<?xml version="1.0" encoding="UTF-8"?><!-- -*- mode: nxml -*- -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:key match="//case" use="@y" name="les_cases"/>
	<xsl:key match="//case" use="@x" name="cols"/>
	<xsl:template	match="/arene">
		<html>
			<head>
				<title>Arene <xsl:value-of select="name" /></title>
				<link rel="icon" type="image/png" href="http://www.starshine-online.com/image/favicon.png" />
				<link href="http://www.starshine-online.com/css/texture.css" rel="stylesheet" type="text/css" />
				<link href="http://www.starshine-online.com/css/texture_low.css" rel="stylesheet" type="text/css" />
				<link href="http://www.starshine-online.com/css/interfacev2.css" rel="stylesheet" type="text/css" />
				<script type="text/javascript"
								src="http://www.starshine-online.com/javascript/jquery/jquery-1.3.2.min.js" />
				<script type="text/javascript"
								src="http://www.starshine-online.com/javascript/jquery/jquery-ui-1.7.2.custom.min.js" />
				<script type="text/javascript"
								src="http://www.starshine-online.com/javascript/fonction.js" />
				<script type="text/javascript"
								src="http://www.starshine-online.com/javascript/overlib/overlib.js" />

			</head>
			<body>
				<xsl:variable name="size">
					<xsl:value-of select="//origin[@size]"/>
				</xsl:variable>
				<div class="div_map">
					<xsl:attribute name="style"><xsl:call-template name="size_px">
						<xsl:with-param name="size_cells" select="//origin/@size" />
					</xsl:call-template></xsl:attribute>
					<xsl:apply-templates />
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template name="size_px">
		<xsl:param name="size_cells" />width: <xsl:value-of
		select="(($size_cells*60)+20+5)" />px; height:<xsl:value-of
		select="(($size_cells*60)+20+5)" />px; </xsl:template>
		<xsl:template match="name" />
	<xsl:template name="top">
		<ul id="map_bord_haut">
			<li id="map_bord_haut_gauche" />
			<xsl:variable name="xorg" select="//origin/@x"/>
			<xsl:for-each select="//case[@x=$xorg]">
				<li>
					<xsl:value-of select="@y"/>
				</li>
			</xsl:for-each>
		</ul>

	</xsl:template>
	<xsl:template match="base">
			<xsl:variable name="base" select="value"/>
	</xsl:template>
	<xsl:template match="cases">
		<xsl:call-template name="top" />
		<xsl:for-each
				select="//case[generate-id()=generate-id(key('les_cases',@y)[1])]">
			<xsl:variable name="row" select="@y"/>
			<ul class="map">
				<li class="map_bord_gauche"><xsl:value-of select="$row"/></li>
				<xsl:for-each select="//case[@y=$row]">
					<xsl:variable name="col" select="@x"/>
					<li>
						<!-- le decor de la case -->
						<xsl:attribute name="class">
							<xsl:text>decor tex</xsl:text><xsl:value-of select="@type"/>
						</xsl:attribute>
			
						<!-- les joueurs -->
						<xsl:if test="//joueur[@y=$row][@x=$col]">
							<div class="map_contenu" onmouseout="return nd();">
								<xsl:attribute name="style">
									<xsl:text>background-image : url(</xsl:text>
									<xsl:value-of select="//base"/>
									<xsl:text>image/personnage/</xsl:text>
									<xsl:value-of select="//joueur[@y=$row][@x=$col][1]/@race"/>
									<xsl:text>/</xsl:text>
									<xsl:value-of select="//joueur[@y=$row][@x=$col][1]/@image"/>
									<xsl:text>.png)</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="onmouseover">
									<!-- We have to not make new lines -->
									<![CDATA[return overlib('<ul>]]><xsl:for-each
									select="//joueur[@y=$row][@x=$col]"><![CDATA[<li class="overlib_joueurs"><span>]]><xsl:value-of
									select="@classe"/><![CDATA[</span>]]><xsl:value-of
									select="@race"/> - Niv. <xsl:value-of
									select="@lvl"/><![CDATA[</li>]]><xsl:value-of
									select="@nom"/></xsl:for-each><![CDATA[</ul>', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');]]>
								</xsl:attribute>
							</div>
						</xsl:if>
					</li>
				</xsl:for-each>
			</ul>
		</xsl:for-each>

			<!--
		<xsl:for-each select="case">
			<![CDATA[<li class="decor tex]]><xsl:value-of select="@type" /><![CDATA[">]]>
			<xsl:text>&lt;/li&gt;</xsl:text>
		</xsl:for-each>
		-->
	</xsl:template>
</xsl:stylesheet>

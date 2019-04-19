<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
	xmlns:html="http://www.w3.org/TR/html5/"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html>
	<head>
		<title>FV Player Video Sitemap</title>
		<meta charset="UTF-8" />
		<style type="text/css">
			body {
				font-family: Helvetica, Arial, sans-serif;
				font-size: 12px;
				color: #545353;
			}
			a img {
				border: none;
			}
			table {
				border: none;
				border-collapse: collapse;
			}
			#sitemap tbody tr:nth-child(odd) {
				background-color: #eee;
			}
			#sitemap tbody tr:hover {
				background-color: #ccc;
			}
			#sitemap tbody tr:hover td, #sitemap tbody tr:hover td a {
				color: #000;
			}
			#content {
				margin: 0 auto;
				width: 1000px;
			}
			p.expl {
				margin: 10px 0px;
				line-height: 1.3em;
			}
			p.expl a {
				color: #da3114;
			}
			a {
				color: #000;
				text-decoration: none;
			}
			a:visited {
				color: #777;
			}
			a:hover {
				text-decoration: underline;
			}
			td {
				font-size:11px;
				padding: 5px 15px 5px 0;
				vertical-align: top;
			}
			td img {
				padding: 0 5px;
			}
			th {
				text-align:left;
				padding-right:30px;
				font-size:11px;
			}
			thead th {
				border-bottom: 1px solid #000;
			}
		</style>
	</head>
	<body>
		<div id="content">
			<h1>FV Player Video Sitemap</h1>
			<p class="expl">
				This sitemap contains <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> URLs.
			</p>
			<div class="content">
				<table id="sitemap">
				<thead>
					<tr>
						<th width="10%">Video</th>
						<th width="20%">Title</th>
						<th width="25%">Description</th>						
						<th width="10%">Category</th>						
						<th width="15%">Pub Date</th>
					</tr>
				</thead>
				<tbody>
					<xsl:for-each select="sitemap:urlset/sitemap:url">
						<tr>
							<xsl:if test="position() mod 2 = 1">
								<xsl:attribute name="class">odd</xsl:attribute>
							</xsl:if>

							<td>
								<xsl:variable name="thumbURL">
									<xsl:value-of select="video:video/video:thumbnail_loc"/>
								</xsl:variable>

								<xsl:variable name="flvURL">
									<xsl:value-of select="video:video/video:player_loc"/>
								</xsl:variable>

								<img src="{$thumbURL}" width="100" />
							</td>

							<td>
								<xsl:variable name="itemURL">
									<xsl:value-of select="sitemap:loc"/>
								</xsl:variable>
								<a href="{$itemURL}">
									<strong><xsl:value-of disable-output-escaping="yes" select="video:video/video:title"/></strong>
								</a>
							</td>

							<td>
								<xsl:variable name="desc">
									<xsl:value-of disable-output-escaping="yes" select="video:video/video:description"/>
								</xsl:variable>
								<xsl:choose>
									<xsl:when test="string-length($desc) &lt; 200">
										<xsl:value-of select="$desc"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="concat(substring($desc,1,200),' ...')"/>
									</xsl:otherwise>
								</xsl:choose>
							</td>

							<td>
								<xsl:value-of select="video:video/video:category"/>
							</td>
              
							<td>
								<xsl:value-of select="concat(substring(video:video/video:publication_date,0,11),concat(' ', substring(video:video/video:publication_date,12,5)))"/>
							</td>
						</tr>
					</xsl:for-each>
					</tbody>
				</table>
			</div>

		</div>
	</body>
</html>
</xsl:template>

</xsl:stylesheet>

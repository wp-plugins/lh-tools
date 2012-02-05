=== LH Tools ===
Contributors: shawfactor
Donate link: http://localhero.biz/plugins/lh-tools/
Tags: rdf, localhero, sparql, skos, triples, api, endpoint
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: trunk

LH Tools is a wordpress plugin that enables a sparql endpoint for for WordPress sites. This will enable sematic querying of WordPress data either from the site itself or from external providers of RDF triples.

== Description ==

LH Tools is a wordpress plugin that enables a sparql endpoint for for WordPress sites. This will enable sematic querying of WordPress data either from the site itself or from external providers of RDF triples.

== Installation ==

Make sure you made a backup of your WordPress database. 
"RDF Tools" creates and uses tables that are completely 
separate from the WordPress ones, but a backup of your
blog data can't hurt nonetheless!


1. Upload "lh-tools" to "/wp-content/plugins/".
2. Download ARC from https://github.com/semsol/arc2/
3. Upload the ARC files to "/wp-content/plugins/lh-tools/arc" 
4. Activate "LH Tools" through the "Plugins" menu in WordPress.
5. Go to "Options" -> "LH Tools" in Wordpress and configure the plugin

== Changelog ==

**0.0.1 February 01, 2012**  
Initial release.

**0.0.2 February 04, 2012**  
Automatically install Arc
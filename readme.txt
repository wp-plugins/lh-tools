=== LH Tools ===
Contributors: shawfactor
Donate link: http://localhero.biz/plugins/lh-tools/
Tags: rdf, localhero, sparql, skos, json, api, endpoint
Requires at least: 3.0
Tested up to: 3.6
Stable tag: trunk

LH Tools is a wordpress plugin that enables a sparql endpoint for for WordPress sites. This will enable semantic querying of WordPress data either from the site itself or from external providers of RDF triples.

== Description ==

LH Tools is a wordpress plugin that enables a sparql endpoint for for WordPress sites. This will enable sematic querying of WordPress data either from the site itself or from external providers of RDF triples.

== Installation ==

Install using WordPress:

1. Log in and go to *Plugins* and click on *Add New*.
2. Search for *LH Tools* and hit the *Install Now* link in the results. WordPress will install it.
3. From the Plugin Management page in Wordpress, activate the *Lh Tools* plugin.
4. Go to *Settings* -> *LH Tools* in the Wordpress menu and specify the settings and activae your Sparql endpoint.

Install manually:

1. Download the plugin zip file and unzip it.
2. Upload the plugin contents into your WordPress installation*s plugin directory on the server. The plugin*s .php files, readme.txt and subfolders should be installed in the *wp-content/plugins/lh-tools/* directory.
3. Download ARC2 from https://github.com/semsol/arc2 and unzip it.
4. Open the unziped folder and upload the entire contents into the */wp-content/plugins/lh-tools/arc/* directory.
5. From the Plugin Management page in Wordpress, activate the *LH Tools* plugin.
6. Go to *Settings* -> *LH Tools* in the Wordpress menu and specify the settings and activate your Sparql endpoint.

== Changelog ==

**0.0.1 February 01, 2012**  
Initial release.

**0.0.2 February 04, 2012**  
Automatically install Arc

**0.0.3 February 04, 2012**  
Bugfix

**0.0.4 February 13, 2012**  
Added the ability to automatically load and spider a RDF source

**0.0.5 February 15, 2012**  
Added install error tracking

**0.0.6 February 15, 2012**  
Added icon

**0.0.7 July 6, 2013**  
Added widgets

**0.0.8 July 6, 2013**  
Improved widgets
<?php

function lh_tools_callback_outputrss($input){

header("HTTP/1.1 200 OK");

header('Content-Type: text/xml');


echo '<?xml version="1.0" encoding="UTF-8" ?>';



?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>

<channel>
<title>LocalHero</title>
<link>http://localhero.biz</link>
<description><?php
print_r($input[result][rows]);

$rows = $input[result][rows];

?>
</description>
<lastBuildDate>Tue, 19 Mar 2013 13:04:52 +0000</lastBuildDate>
<language>en-US</language>
<sy:updatePeriod>hourly</sy:updatePeriod>
<sy:updateFrequency>1</sy:updateFrequency>
<generator>http://wordpress.org/?v=3.5.1</generator>

<?php  

foreach($rows as $row){ 

?>
<item>
<title><?php echo $row[title]; ?></title>
<link><?php echo $row[link]; ?></link>
<pubDate>Tue, 19 Mar 2013 11:23:19 +0000</pubDate>
<dc:creator>root</dc:creator>
<category><![CDATA[Uncategorized]]></category>
<category><![CDATA[IFTTT]]></category>
<guid isPermaLink="false">http://localhero.biz/?p=test</guid>
<description><![CDATA[

foobar

]]></description>

</item>

<?php } ?> 

</channel>
</rss>



<?php


}



define('WP_USE_THEMES', false);

/** Loads the WordPress Environment and Template */
include("../../../wp-blog-header.php");



if(rdf_tools_get_setting('endpoint_active')) {

include_once(ABSPATH . 'wp-content/plugins/lh-tools/arc/ARC2.php');


  $config = array(
    /* db */
    'db_host' => DB_HOST,
    'db_name' => DB_NAME,
    'db_user' => DB_USER,
    'db_pwd' => DB_PASSWORD,
    /* store */
    'store_name' => $table_prefix . 'lh_tools_store',
  'store_allow_extension_functions' => 1,
    /* endpoint */
    'endpoint_features' => rdf_tools_get_setting('endpoint_features'),
    'endpoint_timeout' => rdf_tools_get_setting('endpoint_timeout'),
    'endpoint_max_limit' => rdf_tools_get_setting('endpoint_max_limit'),
    'endpoint_read_key' => rdf_tools_get_setting('endpoint_read_key'),
    'endpoint_write_key' => rdf_tools_get_setting('endpoint_write_key'),
  );


$store = ARC2::getStore($config);

if (function_exists( 'lh_relationships_return_compliant_namespace' ) ){

$namespaces = lh_relationships_return_compliant_namespace();

$query = "";

foreach ($namespaces as $namespace){

$query .= "PREFIX ".$namespace->prefix.": <".$namespace->namespace.">\n";

}

} else {


$query = "PREFIX gn: <http://www.geonames.org/ontology#>
PREFIX wgs84: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX xfn: <http://vocab.sindice.com/xfn#>
PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX moat: <http://moat-project.org/ns#>
PREFIX lh: <http://localhero.biz/uri/localhero-namespace/>
PREFIX admin: <http://webns.net/mvcb/>
PREFIX content: <http://purl.org/rss/1.0/modules/content/>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX sioct: <http://rdfs.org/sioc/types#>
PREFIX tag: <http://www.holygoat.co.uk/owl/redwood/0.1/tags/>
PREFIX georss: <http://www.georss.org/georss>
PREFIX dbp: <http://dbpedia.org/ontology/>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX ore: <http://www.openarchives.org/ore/terms/>
PREFIX lhfts: <http://localhero.biz/namespace/lhformats/>
PREFIX event: <http://purl.org/NET/c4dm/event.owl#>
PREFIX void: <http://rdfs.org/ns/void#> ";


}

$query .= str_replace("\\\"", "\"", $_GET["query"]); 
$foo = str_replace("\\\"", "\"", $_GET['callback']);


$foobar = $store->query($query);

$function = "lh_tools_callback_".$foo;

echo $function($foobar);
}

?>
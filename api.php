<?php


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

//echo "query 1 is ".$query;

$foobar = $store->query($query);

$return = lh_tools_getSPARQLJSONSelectResultDoc($foobar);


header("HTTP/1.1 200 Ok");

if ($_GET["callback"]){

header('Content-Type: application/javascript; charset=utf-8');

echo $_GET["callback"]."(".$return.");";

} else {

header('Content-Type: application/sparql-results+json; charset=utf-8');

echo $return;

}



}

?>
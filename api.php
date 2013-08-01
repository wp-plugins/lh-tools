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


$store = ARC2::getStore($config);

$query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX lh: <http://localhero.biz/uri/localhero-namespace/>
PREFIX rdfs: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX wgs84: <http://www.w3.org/2003/01/geo/wgs84_pos#> ";


$query .= str_replace("\\\"", "\"", $_GET["query"]); 

//echo "query is ".$query;

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
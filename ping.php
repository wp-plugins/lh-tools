<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

if ($_POST["key"] == $config['endpoint_write_key']){

$url = $_POST["url"];

echo "the url is ".$url;

if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    die('Not a valid URL');
} else {

echo "the url is valid";

$hash = lh_tools_return_hash($url);

echo "<br/><strong>the hash is ".$hash."</strong>\n";

$q = "SELECT ?g WHERE  { ?g <http://localhero.biz/#hash_hash> \"".$hash."\" }";

echo $q;

$rs = $store->query($q);

print_r($rs);

if ($rs[result][rows][0][g]){

echo "a hash of this graph ".$insert." already exists\n";

//so delete it

$q = "DELETE FROM <".$rs[result][rows][0][g].">";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

}

$q = "DELETE FROM <".$url.">";

echo $q;

$rs = $store->query($q);

print_r($rs);

$q = "load <".$url.">";

echo $q;

$rs = $store->query($q);

print_r($rs);

$q = "INSERT INTO <".$url."> { <".$url."> <http://localhero.biz/#hash_hash> \"".$hash."\" . }";

echo $q;

$rs = $store->query($q);

print_r($rs);

//Give it a date

$strFormat = 'Y-m-d\TH:i:s.uP';
$strDate = $intDate ? date( $strFormat, $intDate ) : date( $strFormat ) ;
   

$q = "INSERT INTO <".$url."> { <".$url."> <http://purl.org/dc/elements/1.1/date> \"".$strDate."\" . }";

echo $q;

$rs = $store->query($q);

print_r($rs);

echo $graph." loaded\n";


}


} else {

echo "invalid key supplied";

}

}

} else {

echo "sorry we only accept post requests";

}

?>
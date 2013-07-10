<?php

function lh_tools_taxmapper_client($endpoint,$key,$query){

echo $query."\n";

//set POST variables
$fields = array(
'key' => $key,
'query' => $query,
'output' => 'json'
 );
 
//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $endpoint);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));


//execute post
$result = curl_exec($ch);

$result = json_decode($result);

print_r($result);

return $result;

}


function lh_taxmapper_loadpostrdf($postid = null, $user = null){

$permalink = get_permalink($postid);

$graph = $permalink."?feed=lhrdf";

$endpoint = plugins_url().'/lh-tools/';

$key = get_option('rdf_tools_endpoint_write_key');

echo $graph;

$hash = lh_tools_return_hash($insert);

//check for duplicate graphs

$q = "SELECT ?g WHERE  { ?g <http://localhero.biz/#hash_hash> \"".$hash."\" }";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

if ($rs[result][rows][0][g]){


echo "a hash of this graph ".$insert." already exists\n";

//so delete it

$q = "DELETE FROM <".$rs[result][rows][0][g].">";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

}

//delete the existing graph

$q = "DELETE FROM <".$graph.">";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

$q = "load <".$graph.">";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

//ad its hash

$q = "INSERT INTO <".$graph."> { <".$graph."> <http://localhero.biz/#hash_hash> \"".$hash."\" . }";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

//Give it a date

$strFormat = 'Y-m-d\TH:i:s.uP';
$strDate = $intDate ? date( $strFormat, $intDate ) : date( $strFormat ) ;
   

$q = "INSERT INTO <".$graph."> { <".$graph."> <http://purl.org/dc/elements/1.1/date> \"".$strDate."\" . }";

$foo = lh_tools_taxmapper_client($endpoint,$key,$q);

echo $graph." loaded\n";

}


?>
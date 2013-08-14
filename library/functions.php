<?php

function lh_tools_taxmapper_client($endpoint,$key,$query){

echo "<br/><strong>".$query."</strong>\n";

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

echo "<br/><strong>the graph is ".$graph."</strong>\n";

$endpoint = plugins_url().'/lh-tools/';

$key = get_option('rdf_tools_endpoint_write_key');

echo "<br/><strong>the key is ".$key."</strong>\n";

$hash = lh_tools_return_hash($graph);

echo "<br/><strong>the hash is ".$hash."</strong>\n";

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

function lh_tools_loadqueu(){

$query = "select ?o from <queu> where {?s <http://rdfs.org/ns/void#dataDump> ?o }";

}



function lh_tools_escapeJavaScriptText($string){
    return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
}

function lh_tools_getSPARQLJSONSelectResultDoc($r) {
  $vars = $r['result']['variables'];
  $rows = $r['result']['rows'];
  $dur = $r['query_time'];
  $nl = "\n";
  /* doc */
  $r = '{';
  /* head */
  $r .= $nl . '  "head": {';
  $r .= $nl . '    "vars": [';
  $first_var = 1;
  foreach ($vars as $var) {
    $r .= $first_var ? $nl : ',' . $nl;
    $r .= '      "' . $var . '"';
    $first_var = 0;
  }
  $r .= $nl . '    ]';
  $r .= $nl . '  },';
  /* results */
  $r .= $nl . '  "results": {';
  $r .= $nl . '    "bindings": [';
  $first_row = 1;
  foreach ($rows as $row) {
    $r .= $first_row ? $nl : ',' . $nl;
    $r .= '      {';
    $first_var = 1;
    foreach ($vars as $var) {
      if (isset($row[$var])) {
        $r .= $first_var ? $nl : ',' . $nl . $nl;
        $r .= '        "' . $var . '": {';
        if ($row[$var . ' type'] == 'uri') {
          $r .= $nl . '          "type": "uri",';
          $r .= $nl . '          "value": "' . lh_tools_escapeJavaScriptText($row[$var]) . '"';
        }
        elseif ($row[$var . ' type'] == 'bnode') {
          $r .= $nl . '          "type": "bnode",';
          $r .= $nl . '          "value": "' . substr($row[$var], 2) . '"';
        }
        else {
          $dt = isset($row[$var . ' datatype']) ? ',' . $nl . '          "datatype": "' . lh_tools_escapeJavaScriptText($row[$var . ' datatype']) . '"' : '';
          $lang = isset($row[$var . ' lang']) ? ',' . $nl . '          "xml:lang": "' . lh_tools_escapeJavaScriptText($row[$var . ' lang']) . '"' : '';
          $type = $dt ? 'typed-literal' : 'literal';
          $r .= $nl . '          "type": "' . $type . '",';
          $r .= $nl . '          "value": ' . json_encode($row[$var]) . '';
          $r .= $dt . $lang;
        }
        $r .= $nl . '        }';
        $first_var = 0;
      }
    }
    $r .= $nl . '      }';
    $first_row = 0;
  }
  $r .= $nl . '    ]';
  $r .= $nl . '  }';
  /* /doc */
  $r .= $nl . '}';
  return $r;
}





?>
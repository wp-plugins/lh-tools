<?php
/*
Plugin Name: LH Tools
Plugin URI: http://lhero.org/plugins/lh-tools/
Description: RDF Storage and related tools. Requires the <a href="https://github.com/semsol/arc2">ARC Toolkit</a>
Version: 0.15
Author: Peter Shaw
Author URI: http://shawfactor.com/

== Changelog ==

= 0.01 =
* Mapped WP relationships to SIOC triples

= 0.02 =
* Automatically install Arc

= 0.03 =
* Bugfix

= 0.04 =
* added endpoint autoload feature

= 0.05 =
* Added some error tracking

= 0.06 =
* Added icon

= 0.07 =
* Added widgets

= 0.08 =
* Improved widgets

= 0.09 =
* Widget updates

= 0.10 =
* Widget fixes and improved api

= 0.11 =
* Further improved api

= 0.12 =
* Added url mapping to sparql endpoint

= 0.13 =
* Improved map widget

= 0.14 =
* fixes and enhancements

= 0.15 =
* fixes and enhancements

= 0.16 =
* fixes and enhancements


License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2011  Peter Shaw  (email : pete@localhero.biz)


This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* hooks */

add_action('admin_menu', 'lh_tools_handle_admin_request');
//add_action('template_redirect', 'rdf_tools_handle_request');

register_activation_hook(__FILE__, 'lh_tools_activate');
register_deactivation_hook(__FILE__, 'lh_tools_deactivate');
register_activation_hook(__FILE__, 'lh_tools_install_arc' );

/* defines */
define('LH_TOOLS_PLUGIN_DIR', dirname(__FILE__));
define('LH_TOOLS_ARC_URL', 'https://github.com/semsol/arc2/tarball/master');

/* includes */
include_once('library/the_widgets.php');
include_once('library/functions.php');


/* init */

function lh_tools_activate() {
  $flds = lh_tools_get_option_list();
  foreach ($flds as $fld) {
    add_option('rdf_tools_' . $fld, '');
  }
}

function lh_tools_deactivate() {
  $flds = lh_tools_get_option_list();
  foreach ($flds as $fld) {
    delete_option('rdf_tools_' . $fld);
  }
}

/* admin */

function lh_tools_handle_admin_request() {
	add_options_page('LH Tools Setup', 'LH Tools', 10, 'lh-tools.php', 'lh_tools_handle_admin_options');
}

function lh_tools_handle_admin_options() {
  if (isset($_POST['rdf_tools_token']) && ($_POST['rdf_tools_token'] == rdf_tools_get_token())) {
    rdf_tools_handle_options_submit();
  }
  echo lh_tools_get_options_form();
}

function lh_tools_get_options_form() {


if (file_exists(LH_TOOLS_PLUGIN_DIR . '/arc/ARC2.php') ) {
  $r = '
    <div class="wrap rdf-tools">
      <form method="post" action="">
        <input type="hidden" name="rdf_tools_token" value="' . rdf_tools_get_token() . '" />
        <h2>Plugin options for "LH Tools"</h2>
        ' . rdf_tools_get_store_options_fields() . '
        ' . rdf_tools_get_endpoint_options_fields() . '
        <p class="submit">
<input type="submit" name="Submit" value="' . __('Update Options') . '" />
        </p>
      </form>
    </div>
  ';
} else {

$r .= '<p>Arc does not seem to be installed</p>';

$r .= '<p>Usually deactivating and reactivating the plugin will fix this, alternatively you can install Arc manually.</p>';

$r .= '<h3>To install Arc manually</h3>';

$r .= '<ol>
<li>Download ARC2 from https://github.com/semsol/arc2 and unzip it.</li>
<li>Open the unzipped folder and upload the entire contents into the */wp-content/plugins/lh-tools/arc/* directory.</li>
<li>From the Plugin Management page in Wordpress, activate the *LH Tools* plugin.</li>
<li>Go to *Settings* -> *LH Tools* in the Wordpress menu and specify the settings and activae your Sparql endpoint.</li>
</ol>';

}
  return $r;
}

function rdf_tools_get_store_options_fields() {


  $ep = rdf_tools_get_endpoint();

  $r = '';

  if (!$ep->isSetUp()) {
    $r .= '
      <div class="form-item">
        <label>Create the RDF Store</label>
        <span class="field">
          <input type="checkbox" id="store_setup" name="store_setup" value="t" />
          Create the necessary database tables for RDF Storage and SPARQL
        </span>
        <div class="clb"></div>
      </div>
    ';
  } else {
    $r .= '
      <div class="form-item">
        <label>Reset the RDF Store</label>
        <span class="field">
          <input type="checkbox" id="store_reset" name="store_reset" value="t" />
          Delete all data from the RDF Store
        </span>
      </div>
    ';
    $r .= '
      <div class="form-item">
        <label>Delete the RDF Store</label>
        <span class="field">
          <input type="checkbox" id="store_drop" name="store_drop" value="t" />
          Delete the RDF Store (including the database tables)
        </span>
      </div>
    ';
  }
  return '
    <fieldset>
      <legend>Store Options</legend>
      ' . $r .'
    </fieldset>
  ';

}

function lh_tools_get_lh_rdf_get_link(){

if (function_exists('lh_rdf_get_link')){

$foobar = lh_rdf_get_link();

return "or <a href=\"".$foobar."\">".$foobar."</a>";

}

}

function rdf_tools_get_endpoint_options_fields() {


  return '
    <fieldset>
      <legend>SPARQL Endpoint Options</legend>

      <div class="form-item">
        <label for="endpoint_max_limit">Activate</label>
        <span class="field">
          <input type="checkbox" id="endpoint_active" name="endpoint_active" value="t" ' . (rdf_tools_get_setting('endpoint_active') ? 'checked="checked"' : '') . ' />
          Activate the SPARQL Endpoint at <a href="'.plugins_url().'/lh-tools/">'.plugins_url().'/lh-tools/</a>
        </span>
      </div>

      <div class="form-item">
        <label for="endpoint_max_limit">Max number of results</label>
        <span class="field">
          <input type="text" id="endpoint_max_limit" name="endpoint_max_limit" value="' . rdf_tools_get_setting('endpoint_max_limit') . '" />
        </span>
      </div>

      <div class="form-item">
        <label><a href="http://www.w3.org/TR/rdf-sparql-query/">SPARQL Read</a> features</label><br/>

<span class="field">
          ' . rdf_tools_get_endpoint_features_fields(array('select', 'ask', 'construct', 'describe')). '
          Required API key: <input type="text" id="endpoint_read_key" name="endpoint_read_key" value="' . get_option('rdf_tools_endpoint_read_key') . '" />
        </span>
      </div>

      <div class="form-item">
<label><a href="http://arc.semsol.org/docs/v2/sparql+">SPARQL Write</a> features</label><br/>

<span class="field">
          ' . rdf_tools_get_endpoint_features_fields(array('load', 'insert', 'delete')). '
Required API key: <input type="text" id="endpoint_write_key" name="endpoint_write_key" value="' . get_option('rdf_tools_endpoint_write_key') . '" /><br/>
<strong><a href="http://localhero.biz/plugins/lh-tools/lh-tools-load-options/">Load Options</a></strong><br/>
Automatically load rdf from this source: <input type="text" size="70" id="endpoint_src_file" name="endpoint_src_file" value="' . get_option('rdf_tools_endpoint_src_file') . '" /><br/> E.G. <a href="' . get_bloginfo('rdf_url') . '">' . get_bloginfo('rdf_url') . '</a> ' . lh_tools_get_lh_rdf_get_link() . '
        </span>
      </div> 

    </fieldset>
  ';


}

function rdf_tools_get_endpoint_features_fields($flds) {
  $r = '';
  $vals = rdf_tools_get_setting('endpoint_features');
  if (!$vals) $vals = array();
  foreach ($flds as $fld) {
    $chk_code = in_array($fld, $vals) ? ' checked="checked"' : '';
    $r .= '<input type="checkbox" name="endpoint_features[]" value="' . $fld . '"' . $chk_code . ' /> ' . strtoupper($fld) . '<br />';
  }
  return $r;
}

function rdf_tools_get_token() {
  return substr(md5(DB_USER . DB_NAME), -10);
}

function rdf_tools_handle_options_submit() {
  /* store */
  if ($_POST['store_setup'] == 't') {
    $ep = rdf_tools_get_endpoint();
    $ep->setUp();
  }
  if ($_POST['store_reset'] == 't') {
    $ep = rdf_tools_get_endpoint();
    $ep->reset();
  }
  if ($_POST['store_drop'] == 't') {
    $ep = rdf_tools_get_endpoint();
    $ep->drop();
  }
  /* endpoint */
  $settings = lh_tools_get_option_list();
  foreach ($settings as $k) {
    update_option('rdf_tools_' . $k, $_POST[$k]);
  }
}

function lh_tools_get_option_list() {
  return array(
    'endpoint_active',
    'endpoint_max_limit',
    'endpoint_features',
    'endpoint_read_key',
    'endpoint_write_key',
    'endpoint_src_file',
  );
}

/* tools */

function rdf_tools_get_endpoint() {
  global $table_prefix;
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
  return ARC2::getStoreEndpoint($config);
}

function rdf_tools_handle_sparql_request() {
  if(rdf_tools_get_setting('endpoint_active')) {
    $ep = rdf_tools_get_endpoint();
    $ep->go();
  }
  else {
    echo "The endpoint is not activated";
  }
  exit;
}

function rdf_tools_get_setting($name) {
  $r = get_option('rdf_tools_' . $name);
  $r = !$r && ($name == 'endpoint_max_limit') ? 150 : $r;
  return $r;
}

function rdf_tools_get_endpoint_timeout() {
  return 60;
}




/* install ARC2 for sunning the endpoint */
function lh_tools_install_arc(){

if (file_exists(LH_TOOLS_PLUGIN_DIR . '/arc/ARC2.php') || class_exists('ARC2')) {
		return true;
	}

	if (!is_writable(LH_TOOLS_PLUGIN_DIR)) {
		return false;
	}

	$sDir = getcwd();
	chdir(LH_TOOLS_PLUGIN_DIR);

	// download ARC2
	$sTarFileName 	= 'arc.tar.gz';
	$sCmd 			= 'wget --no-check-certificate -T 2 -t 1 -O ' . $sTarFileName . ' ' . LH_TOOLS_ARC_URL . ' 2>&1';
	$aOutput 		= array();
	exec($sCmd, $aOutput, $iResult);
	if ($iResult != 0) {
		chdir($sDir);
		return false;
	}

	// untar the file
	$sCmd 		= 'tar -xvzf ' . $sTarFileName . ' 2>&1';
	$aOutput 	= array();
	exec($sCmd, $aOutput, $iResult);
	if ($iResult != 0) {
		chdir($sDir);
		return false;
	}

	// delete old arc direcotry and tar file
	@rmdir('arc');
	@unlink($sTarFileName);

	// rename the ARC2 folder to arc
	$sCmd		= 'mv semsol-arc2-* arc 2>&1';
	$aOutput 	= array();
	exec($sCmd, $aOutput, $iResult);
	if ($iResult != 0) {
		chdir($sDir);
		return false;
	}
	
	chdir($sDir);
	return true;
}


function lh_tools_return_hash($initialgraph){

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $initialgraph); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$data = curl_exec($ch);
curl_close($ch);

$hash = sha1($data);

return $hash;

}


function lh_tools_insert_graph($insertgraph,$store){

//find the hash of the graph to potentially insert

$hash = lh_tools_return_hash($insertgraph);

//compare hash to other graphs in the store

$q = "SELECT distinct ?g WHERE { GRAPH ?g { ?g <http://localhero.biz/#hash_hash> \"".$hash."\" } }";

$compare_result = $store->query($q);

//if a graph with that hash already exists update hash and date only

if ($compare_result[result][rows][0][g]){

echo "already exists";

$q = "DELETE { <".$insertgraph."> <http://localhero.biz/#hash_hash> ?hash }";

$rs = $store->query($q);


$q = "INSERT INTO <".$insertgraph."> { <".$insertgraph."> <http://localhero.biz/#hash_hash> \"".$hash."\" . }";

$store->query($q);

$q = "DELETE { <".$insertgraph."> <http://purl.org/dc/elements/1.1/date> ?date }";

$rs = $store->query($q);


$strFormat = 'Y-m-d\TH:i:s.uP';
$strDate = $intDate ? date( $strFormat, $intDate ) : date( $strFormat ) ;
   
echo $strDate;

$q = "INSERT INTO <".$insertgraph."> { <".$insertgraph."> <http://purl.org/dc/elements/1.1/date> \"".$strDate."\" . }";

echo $q;

$store->query($q);

echo $insertgraph." has been updated with a new date";


} else {

//Otherwise load the new graph

$q = "LOAD <".$insertgraph."> into <".$insertgraph.">";

$store->query($q);

//ad its hash

$q = "INSERT INTO <".$insertgraph."> { <".$insertgraph."> <http://localhero.biz/#hash_hash> \"".$hash."\" . }";

$store->query($q);

//Give it a date

$strFormat = 'Y-m-d\TH:i:s.uP';
$strDate = $intDate ? date( $strFormat, $intDate ) : date( $strFormat ) ;
   

$q = "INSERT INTO <".$insertgraph."> { <".$insertgraph."> <http://purl.org/dc/elements/1.1/date> \"".$strDate."\" . }";

echo $q;

$store->query($q);

echo $insertgraph." loaded";

}

}




function lh_tools_load_endpoint(){
  
global $table_prefix;

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

$initialgraph = rdf_tools_get_setting('endpoint_src_file');

//Check if the inital graph is already in the store

$store = ARC2::getStore($config);

$q = "SELECT ?hash WHERE { <".$initialgraph."> <http://localhero.biz/#hash_hash> ?hash }";

$feeds = $store->query($q);

$feed = $feeds[result][rows];

if (!$feed[0]){

//If its not there add the initial graph

lh_tools_insert_graph($initialgraph, $store);

} else {

//Otherwise follow local sameas triples which have not been visitted

$parse=  parse_url($initialgraph);

$q = "SELECT * WHERE { ?subject <http://www.w3.org/2000/01/rdf-schema#seeAlso> ?also . OPTIONAL { ?also <http://localhero.biz/#hash_hash> ?hash } FILTER (!bound(?hash)) FILTER  (regex(str(?also),\"^".$parse[scheme]."://".$parse[host]."\")) }";

echo $q;

$rs = $store->query($q);

print_r($rs);

if ($rs[result][rows][0]){

//Load the same as

lh_tools_insert_graph($rs[result][rows][0][also], $store);

} else {

//Otherwise find already loaded graphs and check to see if they need to be updated

$q = "SELECT * WHERE { GRAPH ?g { ?g <http://localhero.biz/#hash_hash> ?hash . ?g <http://purl.org/dc/elements/1.1/date> ?date} } ORDER BY ASC(?date)";

$rs = $store->query($q);

lh_tools_insert_graph($rs[result][rows][0][g], $store);

echo "none left";


}

}



}



$initialgraph = rdf_tools_get_setting('endpoint_src_file');

if ($initialgraph){


add_action('lh_tools_load_endpoint_hourly', 'lh_tools_load_endpoint');

function lh_tools_endpoint_activation() {
if ( !wp_next_scheduled( 'lh_tools_load_endpoint_hourly' ) ) {

wp_schedule_event(time(), 'hourly', 'lh_tools_load_endpoint_hourly');

}
}

add_action('wp', 'lh_tools_endpoint_activation');

}


function lh_tools_weblogUpdates_ping($args) {

global $table_prefix;

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

$initialgraph = rdf_tools_get_setting('endpoint_src_file');

//Check if the inital graph is already in the store

$store = ARC2::getStore($config);

$q = "SELECT * WHERE { GRAPH ?g { ?s ?p ?o . } } LIMIT 10";

$feeds = $store->query($q);

$foo = print_r($feeds, true);

//mail("shawp4@anz.com", "post data", $_SERVER['REMOTE_ADDR']);



$return['flerror'] = false;

$return['message'] = "Thanks for pinging";

return $return;

}

function lh_tools_new_xmlrpc_methods( $methods ) {
    $methods['weblogUpdates.ping'] = 'lh_tools_weblogUpdates_ping';
    return $methods;   
}

add_filter( 'xmlrpc_methods', 'lh_tools_new_xmlrpc_methods');

function lh_tools_init_external_form_handler(){ 
    global $wp_rewrite; 
    $plugin_url = plugins_url( 'index.php', __FILE__ ); 
    $plugin_url = substr( $plugin_url, strlen( home_url() ) + 1 ); 
    // The pattern is prefixed with '^' 
    // The substitution is prefixed with the "home root", at least a '/' 
    // This is equivalent to appending it to `non_wp_rules` 
    $wp_rewrite->add_external_rule( 'sparql-form$', $plugin_url ); 
}

add_action( 'init', 'lh_tools_init_external_form_handler' ); 

function lh_tools_init_external_api_handler(){ 
    global $wp_rewrite; 
    $plugin_url = plugins_url( 'api.php', __FILE__ ); 
    $plugin_url = substr( $plugin_url, strlen( home_url() ) + 1 ); 
    // The pattern is prefixed with '^' 
    // The substitution is prefixed with the "home root", at least a '/' 
    // This is equivalent to appending it to `non_wp_rules` 
    $wp_rewrite->add_external_rule( 'sparql-api$', $plugin_url ); 
}

add_action( 'init', 'lh_tools_init_external_api_handler' ); 


?>
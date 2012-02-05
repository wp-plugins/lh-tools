<?php
/*
Plugin Name: LH Tools
Plugin URI: http://localhero.biz/plugins/lh-tools/
Description: RDF Storage and related tools. Requires the <a href="https://github.com/semsol/arc2">ARC Toolkit</a>
Version: 0.01 (2012-02-01)
Author: Peter Shaw
Author URI: http://shawfactor.com/

== Changelog ==

= 0.01 =
* Mapped WP relationships to SIOC triples

License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2011  Peter Shaw  (email : pete@localhero.biz)


This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published bythe Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* hooks */

add_action('admin_menu', 'rdf_tools_handle_admin_request');
add_action('template_redirect', 'rdf_tools_handle_request');

register_activation_hook('lh-tools/rdf-tools.php', 'rdf_tools_activate');
register_deactivation_hook('lh-tools/rdf-tools.php', 'rdf_tools_deactivate');

/* defines */
define('LH_TOOLS_PLUGIN_DIR', dirname(__FILE__));
define('LH_TOOLS_ARC_URL', 'https://github.com/semsol/arc2/tarball/master');


/* init */

function rdf_tools_activate() {
  $flds = rdf_tools_get_option_list();
  foreach ($flds as $fld) {
    add_option('rdf_tools_' . $fld, '');
  }
lh_tools_install_arc ();
}

function rdf_tools_deactivate() {
  $flds = rdf_tools_get_option_list();
  foreach ($flds as $fld) {
    delete_option('rdf_tools_' . $fld);
  }
}

/* admin */

function rdf_tools_handle_admin_request() {
	add_options_page('LH Tools Setup', 'LH Tools', 10, 'rdf-tools.php', 'rdf_tools_handle_admin_options');
}

function rdf_tools_handle_admin_options() {
  if (isset($_POST['rdf_tools_token']) && ($_POST['rdf_tools_token'] == rdf_tools_get_token())) {
    rdf_tools_handle_options_submit();
  }
  echo rdf_tools_get_options_form();
}

function rdf_tools_get_options_form() {
  $r = '
    <div class="wrap rdf-tools">
      <form method="post" action="">
        <input type="hidden" name="rdf_tools_token" value="' . rdf_tools_get_token() . '" />
        <h2>Plugin options for "LH Tools"</h2>
        <p class="submit">
          <input type="submit" name="Submit" value="' . __('Update Options') . '" />
        </p>
        ' . rdf_tools_get_store_options_fields() . '
        ' . rdf_tools_get_endpoint_options_fields() . '
        <p class="submit">
          <input type="submit" name="Submit" value="' . __('Update Options') . '" />
        </p>
      </form>
    </div>
  ';
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
  }
  else {
    $r .= '
      <div class="form-item">
        <label>Reset the RDF Store</label>
        <span class="field">
          <input type="checkbox" id="store_reset" name="store_reset" value="t" />
          Delete all data from the RDF Store
        </span>
        <div class="clb"></div>
      </div>
    ';
    $r .= '
      <div class="form-item">
        <label>Delete the RDF Store</label>
        <span class="field">
          <input type="checkbox" id="store_drop" name="store_drop" value="t" />
          Delete the RDF Store (including the database tables)
        </span>
        <div class="clb"></div>
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

function rdf_tools_get_endpoint_options_fields() {
  return '
    <fieldset>
      <legend>SPARQL Endpoint Options</legend>

      <div class="form-item">
        <label for="endpoint_max_limit">Activate</label>
        <span class="field">
          <input type="checkbox" id="endpoint_active" name="endpoint_active" value="t" ' . (rdf_tools_get_setting('endpoint_active') ? 'checked="checked"' : '') . ' />
          Activate the SPARQL Endpoint at <a href="' . get_bloginfo('wpurl') .'/wp-content/plugins/lh-tools/">/wp-content/plugins/lh-tools/</a>
        </span>
        <div class="clb"></div>
      </div>

      <div class="form-item">
        <label for="endpoint_max_limit">Max number of results</label>
        <span class="field">
          <input type="text" id="endpoint_max_limit" name="endpoint_max_limit" value="' . rdf_tools_get_setting('endpoint_max_limit') . '" />
        </span>
        <div class="clb"></div>
      </div>

      <div class="form-item">
        <label><a href="http://www.w3.org/TR/rdf-sparql-query/">SPARQL Read</a> features</label>
        <span class="field">
          ' . rdf_tools_get_endpoint_features_fields(array('select', 'ask', 'construct', 'describe')). '
          Required API key: <input type="text" id="endpoint_read_key" name="endpoint_read_key" value="' . get_option('rdf_tools_endpoint_read_key') . '" />
        </span>
        <div class="clb"></div>
      </div>

      <div class="form-item">
        <label><a href="http://arc.semsol.org/docs/v2/sparql+">SPARQL Write</a> features</label>
        <span class="field">
          ' . rdf_tools_get_endpoint_features_fields(array('load', 'insert', 'delete')). '
          Required API key: <input type="text" id="endpoint_write_key" name="endpoint_write_key" value="' . get_option('rdf_tools_endpoint_write_key') . '" />
        </span>
        <div class="clb"></div>
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
  $settings = rdf_tools_get_option_list();
  foreach ($settings as $k) {
    update_option('rdf_tools_' . $k, $_POST[$k]);
  }
}

function rdf_tools_get_option_list() {
  return array(
    'endpoint_active',
    'endpoint_max_limit',
    'endpoint_features',
    'endpoint_read_key',
    'endpoint_write_key',
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
function lh_tools_install_arc () {

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



?>
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

/* instantiation */
$ep = ARC2::getStoreEndpoint($config);

if (!$ep->isSetUp()) {
  $ep->setUp(); /* create MySQL tables */
}

/* request handling */
$ep->go();

}

?>

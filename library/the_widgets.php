<?php

//Article Map Widget/////



function lh_tools_article_map_short_func( $atts ) {

global $post;

if (is_singular()){
	extract( shortcode_atts( array(
		'foo' => 'something',
		'bar' => 'something else',
	), $atts ) );

wp_enqueue_script( 'lh_tools_widgets',   plugins_url().'/lh-tools/scripts/widgets.js', false, false, true );


return "<div id=\"map_canvas\" data-uriref=\"".$post->guid."\" data-lh_tools_endpoint=\"".site_url('/sparql-form')."\" data-lh_tools_dir=\"".plugins_url()."/lh-tools/\"></div>";

}

}

add_shortcode( 'lh_tools_article_map_shortcode', 'lh_tools_article_map_short_func' );


function lh_tools_article_map_widget($args) {
  extract($args);
$foo =  lh_tools_article_map_short_func($args);
echo $foo;
}
 


function lh_tools_article_map_widget_init(){

register_sidebar_widget(__('LH Tools Map Widget'), 'lh_tools_article_map_widget');

}

add_action("plugins_loaded", "lh_tools_article_map_widget_init");



//Related Article Widget/////



function lh_tools_related_articles_short_func( $atts ) {

global $post;

if (is_singular()){

extract( shortcode_atts( array( 'foo' => 'something', 'bar' => 'something else', ), $atts ) );

wp_enqueue_script( 'lh_tools_widgets',   plugins_url().'/lh-tools/scripts/widgets.js', false, false, true );

return "<div id=\"lh_tools_related_articles_div\" data-uriref=\"".$post->guid."\" data-lh_tools_endpoint=\"".site_url('/sparql-form')."\" data-lh_tools_url=\"".plugins_url()."/lh-tools/\"></div>";

}

}

add_shortcode( 'lh_tools_related_articles_shortcode', 'lh_tools_related_articles_short_func' );

function lh_tools_related_articles_widget($args) {
  extract($args);
$foo = lh_tools_related_articles_short_func($args);
echo $foo;
}
 


function lh_tools_related_articles_widget_init(){

register_sidebar_widget(__('LH Tools Related Articles Widget'), 'lh_tools_related_articles_widget');

}

add_action("plugins_loaded", "lh_tools_related_articles_widget_init");






?>
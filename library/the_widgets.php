<?php


function lh_tools_article_map_short_func( $atts ) {

global $post;

if (is_singular()){
	extract( shortcode_atts( array(
		'foo' => 'something',
		'bar' => 'something else',
	), $atts ) );

wp_enqueue_script( 'lh_tools_widgets',   plugins_url().'/lh-tools/scripts/widgets.js', false, false, true );


return "<div id=\"map_canvas\" data-uriref=\"".$post->guid."\" data-lh_tools_url=\"".plugins_url()."/lh-tools/\"></div>";

}

}

add_shortcode( 'lh_tools_article_map', 'lh_tools_article_map_short_func' );


function lh_tools_related_articles_short_func( $atts ) {

global $post;

if (is_singular()){

extract( shortcode_atts( array( 'foo' => 'something', 'bar' => 'something else', ), $atts ) );

wp_enqueue_script( 'lh_tools_widgets',   plugins_url().'/lh-tools/scripts/widgets.js', false, false, true );

return "<div id=\"lh_tools_related_articles_div\" data-uriref=\"".$post->guid."\" data-lh_tools_url=\"".plugins_url()."/lh-tools/\"></div>";

}

}

add_shortcode( 'lh_tools_related_articles_widget', 'lh_tools_related_articles_short_func' );



?>
<?php

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * javascript and css files.
 */

/**
 * bp_present_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_present_add_js() {
	global $bp;
	if ( $bp->current_component == $bp->example->slug )
		wp_enqueue_script( 'bp-present-js', plugins_url( '/bp-present/includes/js/general.js' ) );
}
add_action( 'template_redirect', 'bp_present_add_js', 1 );

?>
<?php
/**
 * Marketify Child Theme
 *
 * Place any custom functionality/code snippets here.
 *
 * @since Marketify Child 1.0
 */

function marketify_child_styles() {
    wp_enqueue_style( 'marketify-child', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'marketify_child_styles', 210 );


function custom_set_post_format_audio( $post_id ) {
	set_post_format( $post_id, 'audio' );
}
add_action( 'fes_submit_submission_form_bottom', 'custom_set_post_format_audio' );

function custom_marketify_page_header_defaults( $args ) {
  $args[ 'size' ] = 'full';
  
  return $args;
}
add_filter( 'marketify_page_header_defaults', 'custom_marketify_page_header_defaults' );


<?php

/*
Plugin Name: MTM WordPress Deployment Updates
Description: Runs update routines allowing code based database updates.
Author: Metal Toad Media
Version: 1.02
*/
add_action( 'admin_init', 'deployment_admin_init' );

/**
 * Attached to init. Runs any necessary update routines
 */
function deployment_admin_init() {

	// What is the current version of this plugin?
	$deployment_version = 3;

	// What is the current version in the db
	$db_version = get_option( 'deployment_version', 0 );

	// Is the db out of date?
	if ( $db_version < $deployment_version ) {

		// If so, loop over all subsequent version numbers and attempt to run corresponding deployment_update_N functions
		for ( $version = $db_version + 1; $version <= $deployment_version; $version ++ ) {
			if ( function_exists( 'deployment_update_' . $version ) ) {
				$success = call_user_func( 'deployment_update_' . $version );

				// If the function returns a boolean false, log an error and bail out. Subsequent updates may rely on this update
				// so we shouldn't proceed any further.
				if ( $success === FALSE ) {
					// @TODO: log error here
					break;
				}
			}

			// If we've reached this far without error, update the db version
			update_option( 'deployment_version', $version );
		}

		// @TODO: output update summary on success
	}
}

/**
 * Update functions.
 */

/**
 * Disable the wordpress-meta-description plugin.
 * Enable and configure the add-meta-tags plugin
 */
function deployment_update_1() {

	require_once ABSPATH . '/wp-admin/includes/plugin.php';

	global $wpdb;
	global $related_post_id;

	//$wpdb->insert( $table, $data, $format );
	$wpdb->insert(
		'wp_posts',
		array(
			'ID'                    => $wpdb->insert_id,
			'post_author'           => 1,
			'post_date'             => '2015-11-02 12:08:30',
			'post_date_gmt'         => '2015-11-02 20:08:30',
			'post_content'          => '',
			'post_title'            => 'Account',
			'post_excerpt'          => '',
			'post_status'           => 'publish',
			'comment_status'        => 'open',
			'ping_status'           => 'open',
			'post_password'         => '',
			'post_name'             => 'my-account',
			'to_ping'               => '',
			'pinged'                => '',
			'post_modified'         => '2015-11-02 12:09:10',
			'post_modified_gmt'     => '2015-11-02 20:09:10',
			'post_content_filtered' => '',
			'post_parent'           => 0,
			'guid'                  => 'https://redacted.dev/logout',
			'menu_order'            => 5,
			'post_type'             => 'nav_menu_item',
			'post_mime_type'        => '',
			'comment_count'         => 0,
		),
		array(
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
		)
	);

	$related_post_id = $wpdb->insert_id;

	$wpdb->query( "INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
	VALUES
		($related_post_id, '_menu_item_type', 'post_type'),
		($related_post_id, '_menu_item_url', ''),
		($related_post_id, '_menu_item_xfn', ''),
		($related_post_id, '_menu_item_classes', 'a:1:{i:0;s:10:\"my-account\";}'),
		($related_post_id, '_menu_item_object_id', '	54'),
		($related_post_id, '_menu_item_object', 'page'),
		($related_post_id, '_menu_item_target', ''),
		($related_post_id, '_menu_item_menu_item_parent', '0')" );

	$wpdb->query( "INSERT INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
	VALUES
		($related_post_id, 43, 0)" );
}

// PS-11dep branched off the deploy branch of 2015-12. modify registration form.
function deployment_update_2() {

	require_once ABSPATH . '/wp-admin/includes/plugin.php';

	// Magic Number: update page 55
	$registration_page = array(
		'ID'           => 55,
		'post_content' => '[theme-my-login register_template="theme-my-login/register-form.php"]',
	);
	wp_update_post( $registration_page );

	// Magic Number: update the username specifications of page 55 to match what's seen on production
	update_post_meta( 55, 'username_specifics', ' ' );

	// Magic Number: update page 53
	$login_page = array(
		'ID'           => 53,
		'post_content' => '[theme-my-login login_template="theme-my-login/login-form.php"]',
	);
	wp_update_post( $login_page );

}

// PS-7 activates a new plugin for photo gallery lightbox
function deployment_update_3() {

	require_once ABSPATH . '/wp-admin/includes/plugin.php';
	activate_plugins( WP_PLUGIN_DIR . '/lightbox-plus/lightboxplus.php' );

	update_option( 'lightboxplus_options', array(
		"lightboxplus_multi"   => "0",
		"use_inline"           => "0",
		"inline_num"           => "5",
		"lightboxplus_style"   => "dark",
		"use_custom_style"     => "1",
		"disable_css"          => "0",
		"hide_about"           => "1",
		"output_htmlv"         => "0",
		"data_name"            => "lightboxplus",
		"load_location"        => "wp_footer",
		"load_priority"        => "10",
		"use_perpage"          => "1",
		"use_forpage"          => "0",
		"use_forpost"          => "1",
		"transition"           => "elastic",
		"speed"                => "300",
		"width"                => "false",
		"height"               => "false",
		"inner_width"          => "false",
		"inner_height"         => "false",
		"initial_width"        => "30%",
		"initial_height"       => "30%",
		"max_width"            => "90%",
		"max_height"           => "90%",
		"resize"               => "1",
		"opacity"              => "0.8",
		"preloading"           => "1",
		"label_image"          => "Image",
		"label_of"             => "of",
		"previous"             => "previous",
		"next"                 => "next",
		"close"                => "close",
		"overlay_close"        => "1",
		"slideshow"            => "1",
		"slideshow_auto"       => "0",
		"slideshow_speed"      => "2500",
		"slideshow_start"      => "start",
		"slideshow_stop"       => "stop",
		"use_caption_title"    => "1",
		"gallery_lightboxplus" => "0",
		"multiple_galleries"   => "0",
		"use_class_method"     => "0",
		"class_name"           => "lbp_primary",
		"no_auto_lightbox"     => "0",
		"text_links"           => "0",
		"no_display_title"     => "0",
		"scrolling"            => "1",
		"photo"                => "0",
		"rel"                  => "0",
		"loop"                 => "1",
		"esc_key"              => "1",
		"arrow_key"            => "1",
		"top"                  => "false",
		"bottom"               => "false",
		"left"                 => "false",
		"right"                => "false",
		"fixed"                => "0",
	) );
}

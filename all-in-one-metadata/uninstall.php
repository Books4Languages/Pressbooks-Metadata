<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/Books4Languages/pressbooks-metadata
 * @since      0.1
 *
 * @package    Pressbooks_Metadata
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//get all the sites for multisite
$blogs_ids=get_sites();

//delete plugin options and posts/chapter related metadata from every site
foreach( $blogs_ids as $b ){
	switch_to_blog( $b->blog_id );

	//get all the options from database
	$all_options = wp_load_alloptions();
	$plugin_options = [];
	$related_meta = [];

	//check if PressBooks plugin is used
	if ((@include_once( WP_PLUGIN_DIR . '/pressbooks/pressbooks.php')) &&
	    //Checking if the plugin is active
	    is_plugin_active('pressbooks/pressbooks.php')) {
		$pb = true;
	}
	else{
		$pb = false;
	}

	//gather all post types, including built-in of without PressBooks
	if($pb){
		$allPostTypes = get_post_types( array( 'public' => true, '_builtin' => false )) ;
	}else{
		$allPostTypes = get_post_types( array( 'public' => true)) ;
	}

	$allPostTypes['site-meta'] = 'site-meta';
	$allPostTypes['metadata'] = 'metadata';


	//extract plugin options from all options
	foreach ( $all_options as $name => $value ) {
		foreach ($allPostTypes as $postType) {
			if ( stristr( $name, '_type_' . $postType ) || stristr( $name, '_type_' . $postType . '_level' ) 
				|| stristr( $name, '_type_overwrite' ) || stristr($name, 'saoverwr') || stristr($name, $postType.'_checkbox')) {
				$plugin_options[ $name ] = $value;

			}
		}
	}


	//delete plugin options
	foreach ( $plugin_options as $key => $value ) {
		if ( get_option( $key ) || get_option($key, 'nonex') !== 'nonex') {
			delete_option( $key );
		}
	}

	// Delete Custom Post Type posts' meta

	//$wpdb->query( "DELETE FROM {$wpdb->postmeta} meta WHERE `meta_key` LIKE '%\_type\_%' OR `meta_key` LIKE '%\_type\_%\_level'" );
}




<?php

/**
 * The -functions.php file is a good place to store miscellaneous functions needed by your plugin.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */

/* Custom Functions go HERE */

/**
 * [bpp_get_user_id_from_blog_id description]
 * @return [type] [description]
 */
function bpp_get_user_id_from_blog_id() {
	$user_from_email = get_user_by('email', get_blog_option( get_current_blog_id(), 'admin_email') );
	return	$user_id = $user_from_email->ID;
}

/**
 * Determine if the site we are looking at is the network site
 *
 * @return [type] [description]
 */
function bpp_is_root_blog() {
	if( function_exists( 'bp_is_root_blog' ) && bp_is_root_blog() ) {
		return true;
	}
	return ( network_site_url('/') == home_url('/') );
}

/**
 * [bpp_is_profile description]
 * @return [type] [description]
 */
function bpp_is_profile() {
	global $bp, $blog_id;

	// Never on the homepage
	if( bpp_is_root_blog() && is_home() ) {
		return false;
	}

	// Look for BuddyPress
	if( isset( $bp->current_component ) && in_array( $bp->current_component, array( 'presentations', 'notifications', 'following', 'followers', 'profile', 'settings' ) ) ) {
		return true;
	}

	return ( is_singular( 'presentations' ) || is_tax( 'presentation' )/* || is_home() || is_front_page()*/ || is_page( 'profile' ) );
}

/**
 * [bpp_redirect_blog_uri_segment description]
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function bpp_redirect_blog_uri_segment($url) {
	return str_replace("/blog", '', $url);
}
add_filter( 'the_permalink', 'bpp_redirect_blog_uri_segment' );

/**
 * [bpp_redirect_homepage description]
 * @return [type] [description]
 */
function bpp_redirect_homepage() {
	if( bpp_is_root_blog() ) { return; }
	if( is_home() ) {
		$user_id = bpp_get_user_id_from_blog_id();
		$user = get_user_by('email', get_blog_option( get_current_blog_id(), 'admin_email') );
		wp_redirect( network_home_url( '/presenters/' . $user->user_login . '/' ), 302 ); exit;
	}
}
add_action( 'pre_get_posts', 'bpp_redirect_homepage' );

/**
 * [bpp_force_displayed_user description]
 * @return [type] [description]
 */
function bpp_force_displayed_user( ) {
	if( bpp_is_root_blog() ) { return; }

	global $bp;

	$bp->displayed_user->id = bpp_get_user_id_from_blog_id();
	$bp->displayed_user->domain = bp_core_get_user_domain( $bp->displayed_user->id );
	$bp->displayed_user->userdata = bp_core_get_core_userdata( $bp->displayed_user->id );
	$bp->displayed_user->fullname = bp_core_get_user_displayname( $bp->displayed_user->id );
}
add_action( 'bp_setup_nav', 'bpp_force_displayed_user', 0 );

function bpp_force_displayed_blog_template() {
	if( is_admin() ) return;
	if( ! bpp_is_root_blog() )	return;

	global $blog_id;
		$user_blogs = get_blogs_of_user( bp_displayed_user_id() );
		$thing = array_shift( $user_blogs  );
		if( $thing->userblog_id == 1 && ! empty( $user_blogs  ) ){
			$thing = array_shift( $user_blogs  );
		}
		$blog_id = $thing->userblog_id;
		switch_to_blog( $blog_id );
}
add_action( 'bp_init', 'bpp_force_displayed_blog_template', 99 );

/**
 * Allow editors to modify the theme customizer
 *
 * [aperture_add_theme_caps description]
 * @return [type] [description]
 */
function bpp_add_theme_caps() {
	$role = get_role( 'editor' );
	$role->add_cap( 'edit_theme_options' );
	$role->add_cap( 'manage_options' );
}
add_action( 'admin_init', 'bpp_add_theme_caps');

/**
 * [aperture_admin_to_editor description]
 * @param  [type] $blog_id [description]
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
function bpp_admin_to_editor( $blog_id, $user_id ) {
	switch_to_blog( $blog_id );
	$user = new WP_User( $user_id );
	if ( $user->exists() ) {
		//$user->set_role( 'administrator' );
		$user->set_role( 'editor' );
	}
	restore_current_blog();
}
add_action( 'wpmu_new_blog', 'bpp_admin_to_editor', 10, 2 );


function bpp_fromemail( $email ) {
	return 'noreply@wppresent.com';
	$wpfrom = get_option('admin_email');
	return $wpfrom;
}
 add_filter('wp_mail_from', 'bpp_fromemail');

function bpp_fromname( $email ) {
	return 'WP Present';
	$wpfrom = get_option('blogname');
	return $wpfrom;
}
add_filter('wp_mail_from_name', 'bpp_fromname');


/**
 * Never hit the registration page
 * @param  [type] $query_vars [description]
 * @return [type]             [description]
 */
function bpp_action_parse_request( $query_vars ) {
	if( 'register' == $query_vars->request || ( isset( $query_vars->query_vars['pagename'] ) && 'register' == $query_vars->query_vars['pagename'] ) ) {
		wp_redirect( network_site_url('/wp-login.php?action=jetpack-sso'), 301 );
		exit;
	}
}
add_action( 'parse_request', 'bpp_action_parse_request', 15 );

function bpp_filter_author_link( $link, $author_id, $author_nicename ) {
	// Replace the author urls with the BP profile urls
	return ( function_exists( 'bp_core_get_user_domain' ) ) ? bp_core_get_user_domain( $author_id ) : $link;
}
//add_filter( 'author_link', 'bpp_filter_author_link', 10, 3 );

/**
 * Return to the site home on logout
 * @return [type] [description]
 */
function bpp_action_logout() {
	wp_redirect( network_site_url() );
	exit();
}
add_action( 'wp_logout' , 'bpp_action_logout' );

/**
 * This is a little bit of a hack / band aid.
 *
 * @todo think of something better
 *
 * @return [type] [description]
 */
function bpp_404_flush() {
	if( is_404() ) {
		flush_rewrite_rules();
	}
}
add_action( 'wp', 'bpp_404_flush' );

/* END Custom Functions */

/**
 * bp_present_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_present_screen_one() when you want to load
 * a template file.
 */
function bp_present_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the example component pages.
	 */
	if ( $bp->current_component != $bp->example->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = BP_PRESENT_PLUGIN_DIR . '/includes/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_present_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_present_load_template_filter', 10, 2 );

/***
 * From now on you will want to add your own functions that are specific to the component you are developing.
 * For example, in this section in the friends component, there would be functions like:
 *    friends_add_friend()
 *    friends_remove_friend()
 *    friends_check_friendship()
 *
 * Some guidelines:
 *    - Don't set up error messages in these functions, just return false if you hit a problem and
 *	deal with error messages in screen or action functions.
 *
 *    - Don't directly query the database in any of these functions. Use database access classes
 * 	or functions in your bp-present-classes.php file to fetch what you need. Spraying database
 * 	access all over your plugin turns into a maintenance nightmare, trust me.
 *
 *    - Try to include add_action() functions within all of these functions. That way others will
 *	find it easy to extend your component without hacking it to pieces.
 */

/**
 * bp_present_accept_terms()
 *
 * Accepts the terms and conditions screen for the logged in user.
 * Records an activity stream item for the user.
 */
function bp_present_accept_terms() {
	global $bp;

	/**
	 * First check the nonce to make sure that the user has initiated this
	 * action. Remember the wp_nonce_url() call? The second parameter is what
	 * you need to check for.
	 */
	check_admin_referer( 'bp_present_accept_terms' );

	/***
	 * Here is a good example of where we can post something to a users activity stream.
	 * The user has excepted the terms on screen two, and now we want to post
	 * "Andy accepted the really exciting terms and conditions!" to the stream.
	 */
	$user_link = bp_core_get_userlink( $bp->loggedin_user->id );

	bp_present_record_activity( array(
		'type' => 'accepted_terms',
		'action' => apply_filters( 'bp_present_accepted_terms_activity_action', sprintf( __( '%s accepted the really exciting terms and conditions!', 'bp-present' ), $user_link ), $user_link ),
	) );

	/* See bp_present_reject_terms() for an explanation of deleting activity items */
	if ( function_exists( 'bp_activity_delete') )
		bp_activity_delete( array( 'type' => 'rejected_terms', 'user_id' => $bp->loggedin_user->id ) );

	/* Add a do_action here so other plugins can hook in */
	do_action( 'bp_present_accept_terms', $bp->loggedin_user->id );

	/***
	 * You'd want to do something here, like set a flag in the database, or set usermeta.
	 * just for the sake of the demo we're going to return true.
	 */

	return true;
}

/**
 * bp_present_reject_terms()
 *
 * Rejects the terms and conditions screen for the logged in user.
 * Records an activity stream item for the user.
 */
function bp_present_reject_terms() {
	global $bp;

	check_admin_referer( 'bp_present_reject_terms' );

	/***
	 * In this example component, the user can reject the terms even after they have
	 * previously accepted them.
	 *
	 * If a user has accepted the terms previously, then this will be in their activity
	 * stream. We don't want both 'accepted' and 'rejected' in the activity stream, so
	 * we should remove references to the user accepting from all activity streams.
	 * A real world example of this would be a user deleting a published blog post.
	 */

	$user_link = bp_core_get_userlink( $bp->loggedin_user->id );

	/* Now record the new 'rejected' activity item */
	bp_present_record_activity( array(
		'type' => 'rejected_terms',
		'action' => apply_filters( 'bp_present_rejected_terms_activity_action', sprintf( __( '%s rejected the really exciting terms and conditions.', 'bp-present' ), $user_link ), $user_link ),
	) );

	/* Delete any accepted_terms activity items for the user */
	if ( function_exists( 'bp_activity_delete') )
		bp_activity_delete( array( 'type' => 'accepted_terms', 'user_id' => $bp->loggedin_user->id ) );

	do_action( 'bp_present_reject_terms', $bp->loggedin_user->id );

	return true;
}

/**
 * bp_present_send_high_five()
 *
 * Sends a high five message to a user. Registers an notification to the user
 * via their notifications menu, as well as sends an email to the user.
 *
 * Also records an activity stream item saying "User 1 high-fived User 2".
 */
function bp_present_send_highfive( $to_user_id, $from_user_id ) {
	global $bp;

	check_admin_referer( 'bp_present_send_high_five' );

	/**
	 * We'll store high-fives as usermeta, so we don't actually need
	 * to do any database querying. If we did, and we were storing them
	 * in a custom DB table, we'd want to reference a function in
	 * bp-present-classes.php that would run the SQL query.
	 */
	delete_user_meta( $to_user_id, 'high-fives' );
	/* Get existing fives */
	$existing_fives = maybe_unserialize( get_user_meta( $to_user_id, 'high-fives', true ) );

	/* Check to see if the user has already high-fived. That's okay, but lets not
	 * store duplicate high-fives in the database. What's the point, right?
	 */
	if ( !in_array( $from_user_id, (array)$existing_fives ) ) {
		$existing_fives[] = (int)$from_user_id;

		/* Now wrap it up and fire it back to the database overlords. */
		update_user_meta( $to_user_id, 'high-fives', serialize( $existing_fives ) );

		// Let's also record it in our custom database tables
		$db_args = array(
			'recipient_id'  => (int)$to_user_id,
			'high_fiver_id' => (int)$from_user_id
		);

		$high_five = new BP_Example_Highfive( $db_args );
		$high_five->save();
	}

	/***
	 * Now we've registered the new high-five, lets work on some notification and activity
	 * stream magic.
	 */

	/***
	 * Post a screen notification to the user's notifications menu.
	 * Remember, like activity streams we need to tell the activity stream component how to format
	 * this notification in bp_present_format_notifications() using the 'new_high_five' action.
	 */
	bp_core_add_notification( $from_user_id, $to_user_id, $bp->example->slug, 'new_high_five' );

	/* Now record the new 'new_high_five' activity item */
	$to_user_link = bp_core_get_userlink( $to_user_id );
	$from_user_link = bp_core_get_userlink( $from_user_id );

	bp_present_record_activity( array(
		'type' => 'rejected_terms',
		'action' => apply_filters( 'bp_present_new_high_five_activity_action', sprintf( __( '%s high-fived %s!', 'bp-present' ), $from_user_link, $to_user_link ), $from_user_link, $to_user_link ),
		'item_id' => $to_user_id,
	) );

	/* We'll use this do_action call to send the email notification. See bp-present-notifications.php */
	do_action( 'bp_present_send_high_five', $to_user_id, $from_user_id );

	return true;
}

/**
 * bp_present_get_highfives_for_user()
 *
 * Returns an array of user ID's for users who have high fived the user passed to the function.
 */
function bp_present_get_highfives_for_user( $user_id ) {
	global $bp;

	if ( !$user_id )
		return false;

	return maybe_unserialize( get_user_meta( $user_id, 'high-fives', true ) );
}


/**
 * bp_present_remove_data()
 *
 * It's always wise to clean up after a user is deleted. This stops the database from filling up with
 * redundant information.
 */
function bp_present_remove_data( $user_id ) {
	/* You'll want to run a function here that will delete all information from any component tables
	   for this $user_id */

	/* Remember to remove usermeta for this component for the user being deleted */
	delete_user_meta( $user_id, 'bp_present_some_setting' );

	do_action( 'bp_present_remove_data', $user_id );
}
add_action( 'wpmu_delete_user', 'bp_present_remove_data', 1 );
add_action( 'delete_user', 'bp_present_remove_data', 1 );

/***
 * Object Caching Support ----
 *
 * It's a good idea to implement object caching support in your component if it is fairly database
 * intensive. This is not a requirement, but it will help ensure your component works better under
 * high load environments.
 *
 * In parts of this example component you will see calls to wp_cache_get() often in template tags
 * or custom loops where database access is common. This is where cached data is being fetched instead
 * of querying the database.
 *
 * However, you will need to make sure the cache is cleared and updated when something changes. For example,
 * the groups component caches groups details (such as description, name, news, number of members etc).
 * But when those details are updated by a group admin, we need to clear the group's cache so the new
 * details are shown when users view the group or find it in search results.
 *
 * We know that there is a do_action() call when the group details are updated called 'groups_settings_updated'
 * and the group_id is passed in that action. We need to create a function that will clear the cache for the
 * group, and then add an action that calls that function when the 'groups_settings_updated' is fired.
 *
 * Example:
 *
 *   function groups_clear_group_object_cache( $group_id ) {
 *	     wp_cache_delete( 'groups_group_' . $group_id );
 *	 }
 *	 add_action( 'groups_settings_updated', 'groups_clear_group_object_cache' );
 *
 * The "'groups_group_' . $group_id" part refers to the unique identifier you gave the cached object in the
 * wp_cache_set() call in your code.
 *
 * If this has completely confused you, check the function documentation here:
 * http://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * If you're still confused, check how it works in other BuddyPress components, or just don't use it,
 * but you should try to if you can (it makes a big difference). :)
 */

?>

<?php
/**
 ** WP Present Admin Bar
 **/
class WP_Present_Admin_Bar {

	const REVISION = 20140209;

	public $plugins_url = '';
	public $nonce_fail_message = '';
	public $capability = 'manage_options';

	// Define and register singleton
	private static $instance = false;
	public static function instance() {
		if( ! self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Clone
	 *
	 * @since 0.9.6
	 */
	private function __clone() { }

	/**
	 * Constructor
	 *
	 * @since 0.9.6
	 */
	private function __construct() {
		add_action( 'admin_head', array( $this, 'action_admin_head' ) );
		add_action( 'wp_head', array( $this, 'action_admin_head' ) );
		add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu_before'), 1 );
		add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu_before_priv'), 1 );
		add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu_after' ), 500 );
		add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu_after_priv' ), 500 );

		add_action( 'after_setup_theme', array( $this, 'action_after_setup_theme' ), 11, 2 );
	}

	/**
	 * [action_after_setup_theme description]
	 * @return [type] [description]
	 */
	function action_after_setup_theme() {
		add_action( 'load-post.php', array( $this, 'theme_layouts_load_meta_boxes' ), 11 );
		add_action( 'load-post-new.php', array( $this, 'theme_layouts_load_meta_boxes' ), 11 );
	}

	/**
	 * [theme_layouts_load_meta_boxes description]
	 * @return [type] [description]
	 */
	function theme_layouts_load_meta_boxes(){

		if( ! isset( $_GET['post_type'] ) || 'presentations' != $_GET['post_type'] ) {
			return;
		}

		/* Add the layout meta box on the 'add_meta_boxes' hook. */
		remove_action( 'add_meta_boxes', 'theme_layouts_add_meta_boxes', 10, 2 );

		/* Saves the post format on the post editing page. */
		remove_action( 'save_post', 'theme_layouts_save_post', 10, 2 );
		remove_action( 'add_attachment', 'theme_layouts_save_post' );
		remove_action( 'edit_attachment', 'theme_layouts_save_post' );
	}

	/**
	 * [action_admin_head description]
	 * @return [type] [description]
	 */
	function action_admin_head() {
		?>
		<style>
			#wpadminbar #wp-admin-bar-network-home .ab-icon:before {
				content: '\f181'; /* WP Present */
				top: -0.1rem;
				font-size: 2rem;
			}
			#wpadminbar #wp-admin-bar-current-user-home .ab-icon:before {
				content: '\f102'; /* Home */
				top: 3px;
			}
			#wp-admin-bar-barfly_edit { margin-right: 150px; }
			#wp-admin-bar-barfly_edit a{ background-color: red; border-right: 1px solid #555; }
		</style>
		<script>
			jQuery(document).ready(function( $ ) {
				$( "#wp-admin-bar-barfly_edit" ).click( function() {
					$( '#draggable' ).toggle();
				});
			});
		</script>
		<?php
	}

	/**
	 * Add items to admin bar before the admin bar has been built
	 *
	 * @uses current_user_can, $wp_admin_bar, add_query_arg, admin_url
	 * @action wp_before_admin_bar_render
	 * @return null
	 */
	function action_admin_bar_menu_before() {
		// Add things
		//$this->add_network_icon();
		//$this->add_edit_button();

		// Super admin ain't got time for that
		if ( is_super_admin() ) {
			return;
		}

		$this->add_network_home_button();
		$this->add_current_user_home_button();
		$this->add_new_presentation_button();
	}

	/**
	 * Add items to admin bar before the admin bar has been built
	 *
	 * @uses current_user_can, $wp_admin_bar, add_query_arg, admin_url
	 * @action wp_before_admin_bar_render
	 * @return null
	 */
	function action_admin_bar_menu_before_priv() {
		global $wp_admin_bar;
		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		if ( !current_user_can( $this->capability ) ) {
			return;
		}
		// Place things for admins only in here
	}

	/**
	 * Add items to admin bar after the admin bar has been built
	 *
	 * @uses current_user_can, $wp_admin_bar, add_query_arg, admin_url
	 * @action wp_before_admin_bar_render
	 * @return null
	 */
	function action_admin_bar_menu_after() {
		global $wp_admin_bar;

		// Super admin ain't got time for that
		if ( is_super_admin() ) {
			return;
		}

		// Removals
		//$wp_admin_bar->remove_menu( 'user-actions' );
		//$wp_admin_bar->remove_menu( 'user-info' );


		$wp_admin_bar->remove_menu( 'wp-logo' );
			$wp_admin_bar->remove_menu( 'about' );
			$wp_admin_bar->remove_menu( 'wporg' );
			$wp_admin_bar->remove_menu( 'documentation' );
			$wp_admin_bar->remove_menu( 'support-forums' );
			$wp_admin_bar->remove_menu( 'feedback' );

		$wp_admin_bar->remove_menu( 'my-sites' );
		$wp_admin_bar->remove_menu( 'my-sites-list' );

		$wp_admin_bar->remove_menu( 'site-name' );

		//$wp_admin_bar->remove_menu( 'comments' );
		$wp_admin_bar->remove_menu( 'new-content' );

		// @TODO: Get this for post types
		if( ! is_singular( 'presentations' ) )
			$wp_admin_bar->remove_menu( 'edit' );

		$wp_admin_bar->remove_menu( 'search' );
	}

	/**
	 * Add items to admin bar after the admin bar has been built
	 *
	 * @uses current_user_can, $wp_admin_bar, add_query_arg, admin_url
	 * @action wp_before_admin_bar_render
	 * @return null
	 */
	function action_admin_bar_menu_after_priv() {
		global $wp_admin_bar;

		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		if ( !current_user_can( $this->capability ) ) {
			return;
		}
		// Place things for admins only in here
	}

	/**
	 * Replace the WP logo with custom icon
	 */
	function add_network_icon() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array (
			'id' => 'barfly-logo',
			'title' => '<img src="' . get_bloginfo('stylesheet_directory') . '/images/icon-fly.png" />',
			'href' => home_url()
		) );
	}

	/**
	 * [add_edit_button description]
	 */
	function add_edit_button() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'parent' => 'top-secondary',
			'id' => 'barfly_edit',
			'title' => 'Edit',
			'href' => '#'
		) );
	}

	/**
	 * [add_network_home_button description]
	 */
	function add_network_home_button() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'network-home',
			'title' => '<span class="ab-icon"></span>WP Present',
			'href' => network_site_url('/')
		) );
	}

	/**
	 * [add_network_home_button description]
	 */
	function add_current_user_home_button() {
		global $wp_admin_bar;

		if( ! is_user_logged_in() ) {
			return;
		}

		if( function_exists( 'bp_loggedin_user_id' ) ) {
			$user_id = bp_loggedin_user_id();
			$user = get_userdata( $user_id );
		}

		if( ! $user_id ) {
			return false;
		}

		// Does the current user already have a blog?
		$user_blogs = get_blogs_of_user( $user_id );
		$username_blog = false;
		if( is_array( $user_blogs ) && count( $user_blogs ) ) {
			foreach( $user_blogs as $key => $user_blog ) {
				if( strtolower( $user->user_login ) == strtolower( $user_blog->blogname ) ) {
					$username_blog = $user_blogs[$key];
				}
			}
		}

		//if ( ! is_admin() ) {
		//	$link = network_site_url('/' . $user->user_login . '/wp-admin/' );
		//} else {
		//	$link = $username_blog->siteurl . '/';
		//}

		$link = network_site_url('/presenters/' . $user->user_login . '/' )	;

		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'current-user-home',
			'title' => '<span class="ab-icon"></span>' . $user->display_name . ' Presents',
			'href' => esc_url( $link )
		) );
	}

	/**
	 * [add_new_presentation_button description]
	 */
	function add_new_presentation_button() {
		global $wp_admin_bar, $bp;

		$user = get_user_by( 'id', bp_loggedin_user_id() );

		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'new_presentation',
			'title' => '+ New Presentation',
			'href' => add_query_arg( 'post_type', 'presentations', network_site_url( $user->user_login . '/wp-admin/post-new.php' ) )
		) );
	}

}
WP_Present_Admin_Bar::instance();

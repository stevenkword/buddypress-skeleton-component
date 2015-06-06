<?php
/**
 ** WP Present Admin Menu
 **/
class WP_Present_Admin_Menu{

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
		add_action( 'admin_menu', array( $this, 'remove_menu_items' ), 9999 );
	}

	/*
	 * Remove Menus
	 */
	function remove_menu_items() {

		// Super admin is special and doesn't need your rules
		if( is_super_admin() ) {
			return;
		}

		global $menu, $submenu;

		//Remove Menus
		$allowed_menu_items = array( __( 'Comments' ), __( 'Presentations' ), __( 'Appearance' ) );
		foreach( $menu as $key => $menu_item ) {
			if( isset( $menu_item[0] ) && ! in_array( $menu_item[0], $allowed_menu_items ) ) {
				unset( $menu[ $key ] );
			}
		}

		//Remove Submenus
		/*
		$allowed_submenu_items = array ();
		$allowed_submenu_items[] = array( 'themes.php', 'customize.php' );
		foreach( $submenu as $key => $submenu_item ) {
			$remove = remove_submenu_page( 'themes.php', 'widgets.php' );
		}
		*/
		remove_submenu_page ('themes.php', 'themes.php');
		remove_submenu_page ('themes.php', 'nav-menus.php');
		remove_submenu_page ('themes.php', 'widgets.php');
		remove_submenu_page ('themes.php', 'custom-header');
		remove_submenu_page( 'themes.php', 'custom-background');
	}

}
WP_Present_Admin_Menu::instance();
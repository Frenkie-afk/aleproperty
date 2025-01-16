<?php
/**
 * see: https://developer.wordpress.org/reference/hooks/plugin_action_links/#user-contributed-notes
 *
*/

class AlepropertySettingsPage
{

	public function __construct()
	{
		add_action('admin_menu', [__CLASS__, 'add_setting_menu']);
		add_filter( 'plugin_action_links', [__CLASS__, 'add_plugin_action_link'], 10, 2 );
	}


	public static function add_setting_menu(): void
	{
		$main_page_hook = add_menu_page(
			esc_html__('Aleproperty Settings', 'aleproperty'),
			esc_html__('Aleproperty', 'aleproperty'),
			'manage_options',
			'aleproperty-settings',
			[__CLASS__, 'render_settings_page'],
			'dashicons-admin-generic',
			100
		);
	}

	/**
	 *  Add settings link to plugin actions
	 */
	public static function add_plugin_action_link( $plugin_actions, $plugin_file  ): array
	{
		$new_actions = [];

		if ( basename( ALE_PROPERTY_PATH ) . '/Aleproperty.php'  === $plugin_file ) { //only for our plugin
			$new_actions['aleproperty_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'ale-property' ), esc_url( admin_url( 'admin.php?page=aleproperty-settings' ) ) );
		}

		return array_merge( $plugin_actions, $new_actions ); // merge arrays and return a new one
	}

	public static function render_settings_page(): void
	{
		require_once ALE_PROPERTY_PATH . '/templates/admin/settings.php';
	}
}

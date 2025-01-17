<?php
/**
 * see: https://developer.wordpress.org/reference/hooks/plugin_action_links/#user-contributed-notes
 * see: https://developer.wordpress.org/reference/functions/register_setting/
 * see: https://developer.wordpress.org/reference/functions/add_settings_section/
 * see: https://developer.wordpress.org/reference/functions/add_settings_field/
 *
*/

class AlepropertySettingsPage
{

	public function __construct()
	{
		add_action('admin_menu', [__CLASS__, 'add_setting_menu']);
		add_filter( 'plugin_action_links', [__CLASS__, 'add_plugin_action_link'], 10, 2 );
		add_action('admin_init', [__CLASS__, 'register_settings']);
	}


	public static function add_setting_menu(): void
	{
		$main_page_hook = add_menu_page(
			esc_html__('Aleproperty Settings', 'aleproperty'),
			esc_html__('Aleproperty', 'aleproperty'),
			'manage_options',
			'aleproperty-settings-page',
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
			$new_actions['aleproperty_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'ale-property' ), esc_url( admin_url( 'admin.php?page=aleproperty-settings-page' ) ) );
		}

		return array_merge( $plugin_actions, $new_actions ); // merge arrays and return a new one
	}

	public static function render_settings_page(): void
	{
		require_once ALE_PROPERTY_PATH . '/templates/admin/settings.php';
	}

	public static function register_settings(): void
	{

		add_settings_section('aleproperty_setting_section', null, null, 'aleproperty-settings-page');

		register_setting(
			'aleproperty_settings',
			'aleproperty_settings', // set also group name to save options as an array
			[
                'type' => 'array',
                'sanitize_callback' => [__CLASS__, 'sanitize_array_settings'],
            ]
		);
		add_settings_field(
			'aleproperty_settings_title',
			__('Title', 'ale-property'),
			[__CLASS__, 'render_text_field'],
			'aleproperty-settings-page',
			'aleproperty_setting_section',
            [   // $args
                'full_name'     => 'aleproperty_settings[aleproperty_settings_title]',
                'value'         =>  get_option('aleproperty_settings')['aleproperty_settings_title'] ?? '',
            ]
		);

		add_settings_field(
			'aleproperty_settings_button',
			__('Show button', 'ale-property'),
			[__CLASS__, 'render_checkbox_field'],
			'aleproperty-settings-page',
			'aleproperty_setting_section',
			[   // $args
				'full_name'     => 'aleproperty_settings[aleproperty_settings_button]',
				'value'         =>  get_option('aleproperty_settings')['aleproperty_settings_button'] ?? 0,
			]
		);

	}

	public static function render_text_field($args): void
	{
        ?>
		<input
            type="text"
            name="<?php echo esc_attr($args['full_name']); ?>"
            value="<?php echo esc_attr( $args['value'] ); ?>"
        >
	<?php }

	public static function render_checkbox_field($args): void
	{
		?>
        <input
            type="checkbox"
            name="<?php echo esc_attr($args['full_name']); ?>"
            value="1"
            <?php checked($args['value'], 1); ?>
        >
	<?php }

	public static function  sanitize_array_settings($input): array
    {
		if (!is_array($input)) {
			return [];
		}

		return array_map('sanitize_text_field', $input); // sanitize all data
	}

}

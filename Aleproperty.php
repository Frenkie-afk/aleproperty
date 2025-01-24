<?php
/*
 * Plugin Name: Ale Property
 * Description: Booking apartments plugin
 * Version: 1.0.0
 * Author: Serhii Afanasiev
 * Author URI: https://github.com/Frenkie-afk
 * Text Domain: ale-property
 * Domain Path: /languages
 * License: GPLv2 or later
 * */

if (!defined('ABSPATH')) {
    exit;
}

/** Global constants */
if ( ! defined( 'ALE_PROPERTY_PATH' ) ) {
    define( 'ALE_PROPERTY_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'ALE_PROPERTY_URL' ) ) {
	define( 'ALE_PROPERTY_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'ALE_PROPERTY_TEMPLATES_PATH' ) ) {
    define( 'ALE_PROPERTY_TEMPLATES_PATH', plugin_dir_path( __FILE__ ) . '/templates' );
}

if ( ! defined( 'ALE_PROPERTY_VERSION' ) ) {
    define( 'ALE_PROPERTY_VERSION', '1.0.0' );
}

require_once ALE_PROPERTY_PATH . '/inc/class-cpt.php';

if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
    require_once ALE_PROPERTY_PATH . '/inc/class-gamajo-template-loader.php';
}

require_once ALE_PROPERTY_PATH . '/inc/class-template-loader.php';
require_once ALE_PROPERTY_PATH . '/inc/class-shortcodes.php';
require_once ALE_PROPERTY_PATH . '/inc/class-widget.php';
require_once ALE_PROPERTY_PATH . '/inc/class-settings-page.php';
require_once ALE_PROPERTY_PATH . '/inc/class-booking-form.php';
require_once ALE_PROPERTY_PATH . '/inc/class-common-helper.php';

class AleProperty
{
    private static AlepropertyTemplateLoader $template_loader;

    function __construct()
    {
       self::init();
    }

    public static function init(): void
    {
        self::init_hooks();
        new AlepropertyCpt(); // instantiate cpt

	    if ( is_admin() ) {
		    new AlepropertySettingsPage(); // instantiate setting page
	    }

        new AlepropertyBookingForm();


        self::$template_loader = new AlepropertyTemplateLoader();
    }
    private static function init_hooks(): void
    {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_scripts']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_front_scripts']);
        add_action('init', [__CLASS__, 'init_action']);
        add_action('pre_get_posts', [__CLASS__, 'adjust_wp_queries']);
        add_action('widgets_init', [__CLASS__, 'register_widgets']);
    }
    public static function init_action(): void
    {
        load_plugin_textdomain('ale-property', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        new AlepropertyShortcodes(); //register shortcodes on init hook
    }

    public static function enqueue_admin_scripts(): void
    {
        wp_enqueue_style( 'aleproperty-admin-style', ALE_PROPERTY_URL . 'assets/css/admin/style.css', [], ALE_PROPERTY_VERSION );
        wp_enqueue_script('aleproperty-admin-scripts', ALE_PROPERTY_URL . 'assets/js/admin/main.js', ['jquery'], ALE_PROPERTY_VERSION, ['strategy' => 'defer', 'in_footer'=> true, ]);
    }

    public static function enqueue_front_scripts(): void
    {
        wp_enqueue_style( 'aleproperty-front-style', ALE_PROPERTY_URL . 'assets/css/front/style.css', [], ALE_PROPERTY_VERSION );
        wp_enqueue_script('aleproperty-front-scripts', ALE_PROPERTY_URL . 'assets/js/front/main.js', ['jquery'], ALE_PROPERTY_VERSION, ['strategy' => 'defer', 'in_footer'=> true, ]);

	    wp_localize_script('aleproperty-front-scripts', 'aleproperty_data', array(
		    'ajax' => [
			    'url'   => admin_url( 'admin-ajax.php' ),
			    'nonce' => wp_create_nonce( 'aleproperty_nonce' ),
		    ],
            'data' => [
                'title' => esc_html__('Booking Form', 'ale-property'),
            ],
            'i18n' => [
                'validation' => [
                    'empty_fields' => esc_html__('Please fill in all required fields', 'ale-property'),
                ]
            ]
	    ));
    }


    public static function activation(): void
    {
        //refresh wp permalinks to prevent 404 error if we have cpt
        flush_rewrite_rules();
    }
    public static function deactivation(): void
    {
        flush_rewrite_rules();
    }

    public static function get_template_loader(): AlepropertyTemplateLoader {
        return self::$template_loader;
    }

    /**
     * Display taxonomy with select options hierarchically (now only 2 levels)
     *
     */
    //todo: it doesn't affect if hierarchy has more than 2 levels. Also move to "Helpers" class
    public static function get_terms_hierarchically($tax_name, $current_term = null): void
    {
        $taxonomy_terms = get_terms( ['taxonomy' => $tax_name, 'hide_empty' => false, 'parent' => 0] );

        foreach ($taxonomy_terms as $term) { ?>
            <option
                value="<?php echo esc_attr($term->term_id);?>"
                <?php selected($current_term, $term->term_id); ?>
            >
                <?php echo esc_html($term->name); ?>
            </option>

            <?php
            $child_terms = get_terms( ['taxonomy' => $tax_name, 'hide_empty' => false, 'parent' => $term->term_id] );

            foreach ($child_terms as $child) : ?>
                <option
                    value="<?php echo esc_attr($child->term_id);?>"
                    <?php selected($current_term, $child->term_id); ?>
                >
                    - <?php echo esc_html($child->name); ?>
                </option>
            <?php endforeach;
        }
    }

    /**
     * Adjust default WP queries
     *
     * */
    public static function adjust_wp_queries($query): void
    {
        if ( !is_admin() && is_post_type_archive('property') && $query->is_main_query() ) {
            $meta_query = $query->get('meta_query') ?: ['relation' => 'AND'];
            $tax_query = $query->get('tax_query') ?: ['relation' => 'AND'];

            $query->set('posts_per_page', get_option( 'posts_per_page' ) ?? 10);
            $query->set('tax_query', ['relation' => 'AND']);
            $query->set('meta_query', ['relation' => 'AND']);

            if (isset($_GET['aleproperty-submit'])) :
                // if selected "type" filter
                if (!empty($_GET['aleproperty-filter-type'])) {
                    $meta_query[] = [
                        'key'   => 'aleproperty_type',
                        'value' => sanitize_text_field($_GET['aleproperty-filter-type']),
                    ];
                }

                //todo: try to add range slider (eg. mobizy)
                if (!empty($_GET['aleproperty-filter-price'])) {
                    $meta_query[] = [
                        'key' => 'aleproperty_price',
                        'value' => sanitize_text_field($_GET['aleproperty-filter-price']),
                        'type' => 'NUMERIC',
                        'compare' => '<=',
                    ];
                }

                if (!empty($_GET['aleproperty-filter-agent'])) {
                    $meta_query[] = [
                        'key' => 'aleproperty_agent',
                        'value' => sanitize_text_field($_GET['aleproperty-filter-agent']),
                    ];
                }

                if (!empty($_GET['aleproperty-filter-location'])) {
                    $tax_query[] = [
                        'taxonomy' => 'location',
                        'terms' => sanitize_text_field($_GET['aleproperty-filter-location']),
                    ];
                }

                if (!empty($_GET['aleproperty-filter-property-type'])) {
                    $tax_query[] = [
                        'taxonomy' => 'property-type',
                        'terms' => sanitize_text_field($_GET['aleproperty-filter-property-type']),
                    ];
                }

                $query->set('meta_query', $meta_query); // set default wp_query parameters for "meta_query"
                $query->set('tax_query', $tax_query); // set default wp_query parameters for "tax_query"

            endif;
        }
    }

    public static function register_widgets(): void
    {
        register_widget('AlepropertyWidget'); // add Widget classname
    }

}

$ale_property = new AleProperty();

register_activation_hook(__FILE__, [$ale_property, 'activation']);
register_deactivation_hook(__FILE__, [$ale_property, 'deactivation']);

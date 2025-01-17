<?php
/**
 * Resources:
 *
 * Post-type registration: https://developer.wordpress.org/reference/functions/register_post_type/
 * Meta-box registration: https://developer.wordpress.org/reference/functions/add_meta_box/
 */

class AlepropertyCpt
{
    public function __construct()
    {
        add_action('init', [$this, 'custom_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_box_property']);
        add_action('save_post', [$this, 'save_meta_box'], 10, 2);
    }

    public function custom_post_type(): void
    {
        // register Property post type
        register_post_type('property', [
            'public'        => true,
            'has_archive'   => true,
            'rewrite'       => ['slug' => 'properties'], // URL slug
            'labels'        => [
                'name'          => esc_html__('Properties', 'ale-property'),
                'add_new_item'  => esc_html__('Add New Property', 'ale-property'),
                'new_item'      => esc_html__('New Property', 'ale-property'),
                'edit_item'     => esc_html__('Edit Property', 'ale-property'),
                'all_items'     => esc_html__('All Properties', 'ale-property'),
                'singular_name' => esc_html__('Property', 'ale-property'),
            ],
            'supports'      => ['title', 'editor', 'thumbnail'],
            'menu_icon'     => 'dashicons-admin-home',
            'show_in_menu'  => true,
            'show_in_rest'  => true,
        ]);

        register_post_type('agent', [
            'public'        => true,
            'has_archive'   => true,
            'rewrite'       => ['slug' => 'agents'], // URL slug
            'labels'        => [
                'name'          => esc_html__('Agents', 'ale-property'),
                'add_new_item'  => esc_html__('Add New Agent', 'ale-property'),
                'new_item'      => esc_html__('New Agent', 'ale-property'),
                'edit_item'     => esc_html__('Edit Agent', 'ale-property'),
                'all_items'     => esc_html__('All Agents', 'ale-property'),
                'singular_name' => esc_html__('Agent', 'ale-property'),
            ],
            'supports'      => ['title', 'editor', 'thumbnail'],
            'menu_icon'     => 'dashicons-groups',
            'show_in_menu'  => true,
            'show_in_rest'  => true,
        ]);

        // register taxonomy location
        register_taxonomy('location', 'property', [
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'locations'],
            'show_in_rest'      => true,
            'labels'            => [
                'name'              => esc_html_x( 'Locations', 'taxonomy general name', 'ale-property' ),
                'singular_name'     => esc_html_x( 'Location', 'taxonomy singular name', 'ale-property' ),
                'search_items'      => esc_html__( 'Search Locations', 'ale-property' ),
                'all_items'         => esc_html__( 'All Locations', 'ale-property' ),
                'parent_item'       => esc_html__( 'Parent Location', 'ale-property' ),
                'parent_item_colon' => esc_html__( 'Parent Location:', 'ale-property' ),
                'edit_item'         => esc_html__( 'Edit Location', 'ale-property' ),
                'update_item'       => esc_html__( 'Update Location', 'ale-property' ),
                'add_new_item'      => esc_html__( 'Add New Location', 'ale-property' ),
                'new_item_name'     => esc_html__( 'New Location Name', 'ale-property' ),
                'menu_name'         => esc_html__( 'Location', 'ale-property' ),
            ]
        ]);

        register_taxonomy('property-type', 'property', [
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'properties-type'],
            'show_in_rest'      => true,
            'labels'            => [
                'name'              => esc_html_x( 'Types', 'taxonomy general name', 'ale-property' ),
                'singular_name'     => esc_html_x( 'Type', 'taxonomy singular name', 'ale-property' ),
                'search_items'      => esc_html__( 'Search Types', 'ale-property' ),
                'all_items'         => esc_html__( 'All Types', 'ale-property' ),
                'parent_item'       => esc_html__( 'Parent Type', 'ale-property' ),
                'parent_item_colon' => esc_html__( 'Parent Type:', 'ale-property' ),
                'edit_item'         => esc_html__( 'Edit Type', 'ale-property' ),
                'update_item'       => esc_html__( 'Update Type', 'ale-property' ),
                'add_new_item'      => esc_html__( 'Add New Type', 'ale-property' ),
                'new_item_name'     => esc_html__( 'New Type Name', 'ale-property' ),
                'menu_name'         => esc_html__( 'Type', 'ale-property' ),
            ]
        ]);
    }

    public function add_meta_box_property(): void
    {
        add_meta_box(
          'aleproperty_settings',
            'Property Settings',
            [$this, 'render_property_settings'],
            'property',
            'normal'
        );
    }

    public function render_property_settings($post): void
    {
        $price = get_post_meta($post->ID, 'aleproperty_price', true);
        $period = get_post_meta($post->ID, 'aleproperty_period', true);
        $type = get_post_meta($post->ID, 'aleproperty_type', true);
        $agent_meta = get_post_meta($post->ID, 'aleproperty_agent', true);
        //get all agents
        $agents = get_posts(['post_type' => 'agent', 'numberposts' => -1]);

        wp_nonce_field('alepropertyfields', '_aleproperty_nonce_field'); // for security purpose

        ob_start(); ?>

        <p>
            <label for="aleproperty_price"><?php esc_html_e('Price', 'ale-property'); ?></label>
            <input type="number" id="aleproperty_price" name="aleproperty_price" value="<?php echo esc_attr($price); ?>">
        </p>

        <p>
            <label for="aleproperty_period"><?php esc_html_e('Period', 'ale-property'); ?></label>
            <input type="text" id="aleproperty_period" name="aleproperty_period" value="<?php echo esc_attr($period); ?>">
        </p>

        <p>
            <label for="aleproperty_type"><?php esc_html_e('Type', 'ale-property'); ?></label>
            <select name="aleproperty_type" id="aleproperty_type">
                <option value="" ><?php esc_html_e('Select Type', 'ale-property'); ?></option>
                <option value="sale" <?php selected('sale', $type); ?>><?php esc_html_e('For Sale', 'ale-property'); ?></option>
                <option value="rent" <?php selected('rent', $type); ?>><?php esc_html_e('For Rent', 'ale-property'); ?></option>
                <option value="sold" <?php selected('sold', $type); ?>><?php esc_html_e('Sold', 'ale-property'); ?></option>
            </select>
        </p>

        <?php if ($agents): ?>
            <p>
                <label for="aleproperty_agent"><?php esc_html_e('Agent', 'ale-property'); ?></label>
                <select name="aleproperty_agent" id="aleproperty_agent">
                    <option value=""><?php esc_html_e('Select Agent', 'ale-property'); ?></option>

                    <?php foreach ($agents as $agent): ?>
                        <option value="<?php echo esc_attr($agent->ID); ?>" <?php selected($agent->ID, $agent_meta); ?>>
                            <?php echo esc_html($agent->post_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php endif; ?>

        <?php echo ob_get_clean();


    }

    public function save_meta_box($post_id, $post): void
    {

        if (!isset($_POST['_aleproperty_nonce_field']) || !wp_verify_nonce($_POST['_aleproperty_nonce_field'], 'alepropertyfields'))
        {
            return;
        }

        //if wp is not doing auto save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        {
            return;
        }

        if ($post->post_type != 'property') {
            return;
        }

        // the same as form input names
        $meta_fields = [
            'aleproperty_price',
            'aleproperty_period',
            'aleproperty_type',
            'aleproperty_agent',
        ];

        foreach ($meta_fields as $meta_key)
        {
            $this->save_meta_fields($post_id, $meta_key);
        }

    }

    private function save_meta_fields($post_id, $meta_key): void
    {
        if (isset($_POST[$meta_key]) && empty($_POST[$meta_key]))
        {
            //todo: check deleting the meta when saving an empty field
            delete_post_meta($post_id, sanitize_text_field($_POST[$meta_key]));
//            wp_die(get_post_meta($post_id, sanitize_text_field($_POST[$meta_key])));
        }
        else
        {
            // update or add meta field
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$meta_key]));
        }
    }
}

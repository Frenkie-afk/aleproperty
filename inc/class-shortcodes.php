<?php
/**
 * Example how to create a shortcode for the plugin
 *
 */
class AlepropertyShortcodes
{

    public function __construct()
    {
        self::register_shortcodes();
    }

    private static function register_shortcodes(): void
    {
        add_shortcode('aleproperty_filter', [__CLASS__, 'filter_shortcode']);
    }
    public static function filter_shortcode( $atts = [] ): string
    {
        $params = shortcode_atts(
            array(
                'location'  => "true",
                'type'      => "true",
                'offer'     => "true",
                'price'     => "true",
                'agent'     => "true",
            ),
            $atts
        );

        ob_start(); ?>


        <div class="filters-form">
            <form method="GET" style="display: flex; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <?php if ($params['price'] === 'true'): ?>
                    <!-- Price filter -->
                    <div>
                        <label for="aleproperty-filter-price" style="display: block"><?php esc_html_e('Maximum price', 'ale-property'); ?></label>

                        <input type="number" name="aleproperty-filter-price" value="<?php echo !empty( $_GET['aleproperty-filter-price'] ) ? $_GET['aleproperty-filter-price'] : ''; ?>" id="aleproperty-filter-price">
                    </div>
                <?php endif; ?>

                <?php if ($params['offer'] === 'true'): ?>
                    <!-- Type filter -->
                    <div>
                        <label for="aleproperty-filter-type" style="display: block"><?php esc_html_e('Select offer type', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-type" id="aleproperty-filter-type">
                            <option value=""><?php esc_html_e('Select offer type', 'ale-property'); ?></option>
                            <option value="sale" <?php echo isset($_GET['aleproperty-filter-type']) && $_GET['aleproperty-filter-type'] === "sale" ? "selected" : ""; ?>><?php esc_html_e('For Sale', 'ale-property'); ?></option>
                            <option value="rent" <?php echo isset($_GET['aleproperty-filter-type']) && $_GET['aleproperty-filter-type'] === "rent" ? "selected" : ""; ?>><?php esc_html_e('For Rent', 'ale-property'); ?></option>
                            <option value="sold" <?php echo isset($_GET['aleproperty-filter-type']) && $_GET['aleproperty-filter-type'] === "sold" ? "selected" : ""; ?>><?php esc_html_e('Sold', 'ale-property'); ?></option>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($params['agent'] === 'true'): ?>
                    <!-- Agent filter -->
                    <div>
                        <label for="aleproperty-filter-agent" style="display: block"><?php esc_html_e('Select agent', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-agent" id="aleproperty-filter-agent">
                            <option value=""><?php esc_html_e('Select agent', 'ale-property'); ?></option>
                            <?php
                            $agent_posts = get_posts( ['post_type' => 'agent', 'numberposts' => -1 ] );
                            $selected_agent = !empty($_GET['aleproperty-filter-agent']) ? $_GET['aleproperty-filter-agent'] : '';

                            foreach ( $agent_posts as $agent ) { ?>
                                <option
                                        value="<?php echo esc_attr($agent->ID); ?>"
                                    <?php selected($selected_agent, $agent->ID); ?>
                                >
                                    <?php echo esc_html($agent->post_name); ?>
                                </option>
                            <?php }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($params['location'] === 'true'): ?>
                    <!-- Location filter -->
                    <div>
                        <label for="aleproperty-filter-location" style="display: block"><?php esc_html_e('Select location', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-location" id="aleproperty-filter-location">
                            <option value=""><?php esc_html_e('Select location', 'ale-property'); ?></option>
                            <?php
                            $selected_location = !empty($_GET['aleproperty-filter-location']) ? $_GET['aleproperty-filter-location'] : '';

                            AleProperty::get_terms_hierarchically('location', $selected_location);

                            ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($params['location'] === 'true'): ?>
                    <!-- Property type filter -->
                    <div>
                        <label for="aleproperty-filter-property-type" style="display: block"><?php esc_html_e('Select property type', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-property-type" id="aleproperty-filter-property-type">
                            <option value=""><?php esc_html_e('Select property type', 'ale-property'); ?></option>
                            <?php
                            $selected_property_type = !empty($_GET['aleproperty-filter-property-type']) ? $_GET['aleproperty-filter-property-type'] : '';

                            AleProperty::get_terms_hierarchically('property-type', $selected_property_type);

                            ?>
                        </select>
                    </div>
                <?php endif; ?>

            </form>
        </div>

        <?php return ob_get_clean();
    }
}
